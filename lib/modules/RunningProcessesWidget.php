<?php

class RunningProcessesWidget extends CorePlugin implements PortalPlugin
{
    public function getPluginName()
    {
        return _('Meine Prozesse');
    }

    public function getMetadata()
    {
        return [
            'description' => _('Dieses Widget zeigt offene Prozesse, wie unbearbeitete Fragebögen oder zeitgesteuerte Dateiordner, aus Ihren Veranstaltungen oder Einrichtungen an.')
        ];
    }

    function getPortalTemplate()
    {
        $controller = studipApp(\Trails\Dispatcher::class)->load_controller('running_processes');
        $response = $controller->relayWithRedirect('running_processes/widget');
        $template = $GLOBALS['template_factory']->open('shared/string');
        $template->content = $response->body;

        return $template;
    }
}
