<?php
/*
 * CacheException.class.php
 * This file is part of Stud.IP.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2024
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       6.0
 */

namespace Studip\Cache;


/**
 * The InvalidCacheArgumentException is an implementation of the InvalidArgumentException interface
 *  of PSR-6 that behaves like a StudipException.
 */
class InvalidCacheArgumentException extends \StudipException implements \Psr\Cache\InvalidArgumentException
{
    //Nothing here, since there is nothing to implement.
}
