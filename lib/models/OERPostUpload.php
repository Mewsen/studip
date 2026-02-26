<?php

/**
 * Model class to handle reminder for possible OER upload files
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Michaela Brückner <brueckner@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       5.3
 * @property string file_ref_id database column
 * @property string user_id database column
 * @property int reminder_date database column
 * @property string mkdate database column
 * @property string chdate database column
 */
class OERPostUpload extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'oer_post_upload';
        $config['belongs_to']['fileref'] = [
            'class_name' => FileRef::class,
            'foreign_key' => 'file_ref_id',
            'on_delete' => 'delete'
        ];

        parent::configure($config);
    }

}
