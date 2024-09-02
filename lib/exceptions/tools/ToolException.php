<?php
/**
 * ToolException.class.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2023
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

namespace Studip;

/**
 * ToolException is for exceptions that occur in the plugin management
 * or the core course tools (modules).
 */
class ToolException extends Exception
{
    /**
     * TOOL_NOT_ACTIVATED means that a tool or plugin shall be loaded
     * or used which is not activated.
     */
    const TOOL_NOT_ACTIVATED = 1;
}
