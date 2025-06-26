<?php

/**
 * This exception is thrown, if a plugin is already loading.
 *
 * There should always only be one instance of a plugin class.
 */
class PluginAlreadyLoadingException extends Exception
{
    public function __construct(string $pluginClassName)
    {
       parent::__construct(
            sprintf(
                _("Plugin '%s' wird bereits geladen."),
                $pluginClassName
            )
        );
    }
}
