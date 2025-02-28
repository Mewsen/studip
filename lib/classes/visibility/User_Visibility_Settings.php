<?php
/**
 * PrivacySetting.php - Represents ONE User_Visibility_Settings
 *
 * The PrivacySetting class is one privacySettings in the UserPrivacyTree
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 *
 * @property int $id
 * @property int $visibility_id
 * @property int $parent_id
 * @property string $category
 * @property string $name
 * @property int|null $state
 * @property int|null $plugin
 * @property string $identifier
 */
class User_Visibility_Settings extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'user_visibility_settings';

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
        ];

        parent::configure($config);
    }

    // parent of the Visibility
    public $parent;

    // children of the visibility
    public $children = [];

    // determines if the option is displayed in settings
    public $displayed = false;

    /**
     * Find a User_Visibility_Setting by an id or an identifier and a user
     *
     * @param ?string $id
     */
    public static function find($id = null, $userid = null) {

        // If we have no id or we have a real int id use standard construction
        if (!$id || is_int($id)) {
            parent::find($id);
        } else {

            // Rewrite user if nessecary
            $userid = $userid ? : $GLOBALS['user']->id;

            // Return the first (and only) matching visibility setting
            return self::findOneBySQL('user_id = ? AND identifier = ? LIMIT 1', [$userid, $id]);
        }
    }

    /**
     * Recursive load all Children
     */
    public function loadChildren()
    {
        $this->children = User_Visibility_Settings::findBySQL("user_id = ? AND parent_id = ? ", [$this->user_id, $this->visibilityid]);
        foreach ($this->children as $child) {
            $child->parent = $this;
            $child->loadChildren();
        }
    }

    /**
     * Check if the option needs to be displayed in settings. Recursively also
     * set all parents to displayed
     */
    private function displayCheck()
    {
        // check if it is a category
        $catDisplay = $this->category != 0;

        // check if it is a plugin and if it is activated
        $pluginManager = PluginManager::getInstance();
        $plugin = $pluginManager->getPluginInfoById($this->plugin);
        $pluginDisplay = ($this->plugin == 0 || ($pluginManager->isPluginActivatedForUser($this->plugin, $this->user_id)) && $plugin['enabled']);

        // now check both
        if ($catDisplay && $pluginDisplay) {
            $this->setDisplayed();
        }
    }

    /**
     * Categories without childs are not displayed. Whenever a child in a tree
     * needs to be displayed the whole tree has to be displayed.
     */
    public function setDisplayed()
    {
        $this->displayed = true;
        if ($this->parent_id != 0) {
            $this->parent->setDisplayed();
        }
    }

    /**
     * Returns the needed Arguments to build up the Interface
     * @param array $result the given array where the setting stores its data
     * @param int $depth the depth of the setting in the settingstree
     */
    public function getHTMLArgs(&$result, $depth = 0)
    {
        $mapping = [
            'commondata'     => _('Allgemeine Daten'),
            'privatedata'    => _('Private Daten'),
            'studdata'       => _('Studien-/Einrichtungsdaten'),
            'additionaldata' => _('Zusätzliche Datenfelder'),
            'owncategory'    => _('Eigene Kategorien'),

            'picture' => _('Eigenes Bild'),
            'motto' => _('Motto'),
            'private_phone' => _('Private Telefonnummer'),
            'private_cell' => _('Private Handynummer'),
            'privadr' => _('Private Adresse'),
            'homepage' => _('Homepage-Adresse'),
            'news' => _('Ankündigungen'),
            'termine' => _('Termine'),
            'votes' => _('Fragebögen'),
            'studying' => _('Wo ich studiere'),
            'lebenslauf' => _('Lebenslauf'),
            'hobby' => _('Hobbys'),
            'publi' => _('Publikationen'),
            'schwerp' => _('Schwerpunkte'),
        ];

        if ($this->displayed) {
            $entry = [];
            $entry['is_header'] = $this->category == 0 && $this->parent_id == 0;
            $entry['is_category'] = $this->category == 0;
            $entry['id'] = $this->visibilityid;
            $entry['state'] = $this->state;
            $entry['padding'] = ($depth * 20) . "px";
            $entry['name'] = $mapping[$this->identifier] ?? $this->name ?? '';
            $result[] = $entry;

            // Now add the html args for the children
            foreach ($this->children as $child) {
                $child->getHTMLArgs($result, $depth + 1);
            }
        }
    }
}
