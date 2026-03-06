<?php
/**
 * Study group widget. Displays a list of possibly interesting study groups
 *
 * @author
 * @license GPL2 or any later version
 * @since   Stud.IP 6.0
 */

class StudygroupWidget extends CorePlugin implements PortalPlugin
{
    public function getPluginName()
    {
        return _('Für dich vorgeschlagene Studiengruppen');
    }

    public function getMetadata()
    {
        return [
            'description' => _('Dieses Widget zeigt eine Liste von Vorschlägen interessanter Studiengruppen an.')
        ];
    }

    public function getPortalTemplate()
    {
        $template = $GLOBALS['template_factory']->open('start/studygroups');

        $controller = studipApp(\Trails\Dispatcher::class)->load_controller('my_studygroups');
        $response = $controller->relayWithRedirect('my_studygroups/proposals');
        $template->proposals = $response->body;

        $navigation = new Navigation('', 'dispatch.php/course/wizard?studygroup=1');
        $navigation->setImage(Icon::create('add', Icon::ROLE_CLICKABLE, ['title' => _('Neue Studiengruppe anlegen')]));
        $navigation->setLinkAttributes(['data-dialog' => 'reload-on-close']);
        $template->icons = [$navigation];

        return $template;
    }
}
