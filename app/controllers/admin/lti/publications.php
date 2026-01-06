<?php
require_once __DIR__ . '/AdminBaseController.php';

use LTI\AdminBaseController;
use Lti\Publication;

class Admin_Lti_PublicationsController extends AdminBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('/course/lti/publications');

        $this->buildPublicationsSidebar();
    }

    public function index_action(): void
    {
        PageLayout::setTitle(_('LTI-Veröffentlichungen'));

        $sqlQuery = [
            "`range_id` = :range_id ORDER BY `mkdate`, `name`",
            [
                'range_id' => $this->range_id
            ]
        ];

        if ($GLOBALS['perm']->have_perm('root')) {
            $sqlQuery = [
                "TRUE ORDER BY `mkdate`, `name`"
            ];
        }

        $publications = Publication::findBySQL(...$sqlQuery);

        $this->render_vue_app(
            Studip\VueApp::create('lti/publications/Index')
                ->withProps([
                    'publications' => array_map(fn ($p) => $p->transformData(), $publications)
                ])
        );
    }

    public function create_action(): void
    {
        PageLayout::setTitle(_('Neues Deployment anlegen'));
        $this->render_vue_app(
            Studip\VueApp::create('lti/publications/Create')
        );
    }
}
