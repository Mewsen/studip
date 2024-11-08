<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4766
 */
return new class extends Migration {
    public function description()
    {
        return 'Fix container payload, in case an array was inserted into the block list (see BIEST#4766)';
    }

    protected function up()
    {
        $db = DBManager::get();

        $query = "SELECT * FROM `cw_containers`
                WHERE payload LIKE '%\"blocks\":%'
                AND (
                    payload LIKE '%\"blocks\":[%[%]%'
                    OR 
                    payload LIKE '%\"blocks\":[%[^\"]%'
                );
        ";
        $containers = $db->fetchAll($query);

        $update_container = $db->prepare("UPDATE `cw_containers` SET `payload` = ? WHERE `id` = ?");

        foreach ($containers as $container) {
            $payload = json_decode($container['payload'], true);
            $sections = $payload['sections'];
            foreach ($sections as &$section) {
                $section['blocks'] = array_map(function ($item) {
                    if (is_array($item)) {
                        return implode('', array_map('strval', $item));
                    }
                    return strval($item);
                }, $section['blocks']);
                ;
            }
            $payload['sections'] = $sections;
            $payload = json_encode($payload);
            $id = $container['id'];
            $update_container->execute([$payload, $id]);
        }
    }

};
