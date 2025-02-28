<?php

namespace MassMail;

use \User, \DBManager, \StudipPDO, \PDO;

class MassMailMarker extends \SimpleORMap
{

    /*
     * This seems to be necessary because of the direct reference in getDescription.
     * Otherwise a PHP warning is thrown.
     */
    public string $description;

    protected static function configure($config = [])
    {
        $config['db_table'] = 'massmail_markers';

        parent::configure($config);
    }

    public static function findAll($root = false) {
        return $root
            ? static::findBySQL("1 ORDER BY `position`")
            : static::findbyRoot_only("0 ORDER BY `position`");
    }

    /**
     * Replaces markers contained in the given text with their replacement value for the given user.
     *
     * @param string $text
     * @param User $user
     * @param MassMailMarker[] $markers
     * @return string|string[]
     */
    public static function processText(string $text, User $user, array $markers) {
        $find = [];
        $replace = [];
        foreach ($markers as $marker) {
            if ((!$marker->root_only || MassMailPermission::has(User::findCurrent()->id, true))
                && strpos($text, '{{' . $marker->marker . '}}') !== false
                && $marker->type != 'token') {
                $find[] = '{{' . $marker->marker . '}}';
                $replace[] = $marker->replaceMarker($user);
            }
        }
        $text = str_replace($find, $replace, $text);
        return $text;
    }

    /**
     * Replaces tokens in the given text with a token for the given user.
     * @param int $message_id
     * @param string $text
     * @param User $user
     * @return string|string[]
     */
    public static function processToken(int $message_id, string $text, User $user)
    {
        foreach (self::findByType('token') as $marker) {
            if ((!$marker->root_only || MassMailPermission::has(User::findCurrent()->id, true)) &&
                strpos($text, '{{' . $marker->marker . '}}') !== false) {
                $text = str_replace('{{' . $marker->marker . '}}',
                    $marker->getReplacementToken($message_id, $user),
                    $text
                );
            }
        }
        return $text;
    }

    /**
     * This is a helper get function which gets the translated marker description. As the regular i18 mechanism for
     * translateable content is not working here (thie is just shown in the GUI but stored dynamically in the database)
     * I really do not know how to do that otherwise.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return _($this->description);
    }

    /**
     * Replaces the current marker text according to the given user.
     * @param User $user
     * @return array|mixed|\SimpleORMapCollection|string|string[]|void|null
     */
    public function replaceMarker(User $user)
    {
        $replacement = $this->replacement;

        switch ($user->geschlecht) {
            case 2:
                if ($this->replacement_female) {
                    $replacement = $this->replacement_female;
                }
                break;
            case 0:
            case 3:
                if ($this->replacement_unknown) {
                    $replacement = $this->replacement_unknown;
                }
                break;
        }

        switch ($this->type) {
            // Just plain text replacing the marker, just check if other markers are included here.
            case 'text':
                if (strpos($replacement, '{{') !== false) {
                    $matches = [];
                    preg_match_all('/{{([a-zA-Z0-9\-_]+)}}/m', $replacement, $matches);
                    foreach ($matches[1] as $match) {
                        $replacement = str_replace('{{' . $match . '}}',
                            MassMailMarker::findOneByMarker(trim($match))->replaceMarker($user),
                            $replacement
                        );
                    }
                }
                return $replacement;

            // Content from one or more database columns replaces the marker.
            case 'database':
                $data = words($replacement);
                $find = [];
                $replace = [];
                foreach ($data as $entry) {
                    if (strpos($entry, '{') !== false) {
                        $matches = [];
                        preg_match_all('/{{([a-zA-Z0-9\-_]+)}}/m', $entry, $matches);
                        foreach ($matches[1] as $match) {
                            $replacement = str_replace($entry,
                                MassMailMarker::findOneByMarker(trim($match))->replaceMarker($user),
                                $replacement
                            );
                        }
                    } else {
                        // Extract the database fields...
                        [$table, $column] = explode('.', $entry);
                        // ... and query database for values to insert.
                        $stmt = DBManager::get()->prepare("SELECT `:column`
                                FROM `:table` WHERE `user_id` = :userid LIMIT 1");
                        $stmt->bindParam('column', $column, StudipPDO::PARAM_COLUMN);
                        $stmt->bindParam('table', $table, StudipPDO::PARAM_COLUMN);
                        $stmt->bindParam('userid', $user->id);
                        $stmt->execute();
                        $dbdata = $stmt->fetch(PDO::FETCH_ASSOC);
                        $replacement = str_replace($entry, $dbdata[$column], $replacement);
                    }
                }
                // If we have empty values from database, there could be excess whitespace -> remove.
                return trim(preg_replace('/(\s)+/', ' ', $replacement));

            // The marker is replaced by the result of a function call.
            case 'function':
                $data = words($replacement);
                $function = array_shift($data);
                return call_user_func_array($function, $data);
        }
    }

    /**
     * Gets a token and assigns it to the given user.
     */
    public function getReplacementToken($message_id, $user): string
    {
        $token = MassMailToken::findOneBySQL(
            "`message_id` = :id AND `user_id`IS NULL"
        );

        if ($token) {
            $token->user_id = $user->id;
            $token->store();
            return $token->token;
        } else {
            throw new \Exception('No free token available.');
        }
    }

}
