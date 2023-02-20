<?php


/**
 * StudygroupAvatar.class.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */
class StudygroupAvatar extends CourseAvatar
{
    /**
     * @inheritdoc
     */
    static function getAvatar($id)
    {
        return new StudygroupAvatar($id);
    }

    /**
     * @inheritdoc
     */
    static function getNobody()
    {
        return new StudygroupAvatar('nobody');
    }


    /**
     * @inheritdoc
     */
    protected function generateFileName($user_id, $size, $ext = 'png', $retina = false)
    {
        if ($user_id === Avatar::NOBODY) {
            $user_id = 'studygroup';
        }
        return parent::generateFileName($user_id, $size, $ext, $retina);
    }
}
