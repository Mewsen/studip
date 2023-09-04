<?php

namespace JsonApi\Models;

class Studip
{
    public function getId()
    {
        return 'studip';
    }

    public function getProperties()
    {
        $properties = [
            new StudipProperty('studip-version', 'Stud.IP-Version', $GLOBALS['SOFTWARE_VERSION']),
            new StudipProperty('OERCAMPUS_VISIBLE', 'Is oer campus visible with current user permission?', $GLOBALS['perm']->have_perm(\Config::get()->OER_PUBLIC_STATUS)),
        ];

        $oerCampusEnabled = self::getConfigOption('OERCAMPUS_ENABLED');
        if ($oerCampusEnabled) {
            $properties[] = $oerCampusEnabled;
        }

        $oerEnableSuggestions = self::getConfigOption('OER_ENABLE_SUGGESTIONS');
        if ($oerEnableSuggestions) {
            $properties[] = $oerEnableSuggestions;
        }

        $copyrightDialog = self::getConfigOption('COPYRIGHT_DIALOG_ON_UPLOAD');
        if ($copyrightDialog) {
            $properties[] = $copyrightDialog;
        }

        return $properties;
    }

    private static function getConfigOption($field)
    {
        $config = \Config::get();

        if (!isset($config[$field])) {
            return null;
        }

        $description = $config->getMetadata($field)['description'];
        $value = $config->getValue($field);

        return new StudipProperty($field, $description, $value);
    }
}
