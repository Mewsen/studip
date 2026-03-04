<?php

/**
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 * @var array $plugin
 * @var string $sticky
 * @var string $plugin_id
 * @var string $activated
 */
?>
<div class="plugin<?= ($plugin['enabled'] ? "" : " deactivated") ?>" <?= $plugin['enabled'] ? "" : ' title="'._("Plugin ist momentan global deaktiviert.").'"' ?>>
    <h2><?= htmlReady($plugin['name']) ?></h2>
    <div>
        <input type="hidden" value="1" name="<?= "modules[$plugin_id][sticky]" ?>">
        <input type="hidden" value="0" name="<?= "modules[$plugin_id][activated]" ?>">
        <label><input type="checkbox" value="0" name="<?= "modules[$plugin_id][sticky]" ?>"<?= !$sticky ? " checked" : "" ?>><?= _("Wählbar") ?></label>
        <label><input type="checkbox" value="1" name="<?= "modules[$plugin_id][activated]" ?>"<?= $activated ? " checked" : "" ?>><?= _("Standard aktiv") ?></label>
    </div>
</div>
