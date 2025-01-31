<?php
/**
 * Visibility_Extern.php - Verifies if an user may see the extern visibilities
 *
 * By now everything that is marked as visible extern is visible
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
class Visibility_Extern extends VisibilityAbstract{

    // Should this state be used?
    protected $activated = true;

    // What number does this state get in the database?
    protected $int_representation = 5;

    public function __construct()
    {
        $this->display_name = _('externe Seiten');
        $this->description = _('auf externen Seiten sichtbar');
    }

    // When do two users have this state
    public function verify($user_id, $other_id)
    {
        return true;
    }
}
