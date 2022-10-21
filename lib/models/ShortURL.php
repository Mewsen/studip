<?php


use Hashids\Hashids;


/**
 * A SimpleORMap class for short URLs.
 *
 * @property string id The ID of the short URL.
 * @property string alias
 * @property string url The URL where the short URL leads to.
 * @property string user_id The ID of the user who created the short URL.
 * @property string mkdate The creation timestamp of the short URL.
 * @property string chdate The modification timestamp of the short URL.
 */
class ShortURL extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'short_urls';
        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id'
        ];
        $config['registered_callbacks']['after_store'][] = 'cbGenerateAlias';

        parent::configure($config);
    }


    public function cbGenerateAlias()
    {
        if (!$this->alias) {
            //Generate the alias from the ID.
            $hash_id = new Hashids($GLOBALS['UNI_NAME_CLEAN'], 8);
            $this->alias = $hash_id->encode($this->id);
            if ($this->isDirty()) {
                $this->store();
            }
        }
    }
}
