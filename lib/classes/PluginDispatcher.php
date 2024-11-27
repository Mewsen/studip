<?php
/**
 * This is a specialized dispatcher for plugins.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since Stud.IP 6.0
 */
class PluginDispatcher extends StudipDispatcher
{
    public StudIPPlugin $current_plugin;

    public function __construct(
        \Psr\Container\ContainerInterface $container,
        StudIPPlugin $plugin
    ) {
        parent::__construct($container);

        $this->current_plugin = $plugin;

        $this->trails_root = $plugin->getPluginPath();
        $this->trails_uri = rtrim(PluginEngine::getLink($this->current_plugin, [], null, true), '/');
        $this->default_controller = 'index';
    }
}
