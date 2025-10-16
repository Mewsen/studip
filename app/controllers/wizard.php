<?php

class WizardController extends AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        $GLOBALS['perm']->check('root');

        parent::before_filter($action, $args);
    }

    public function index_action()
    {
        PageLayout::setTitle('Wizard');

        $this->render_wizard([
            [
                'id' => 'massmail',
                'name' => 'Nachrichtenübersicht',
                'icon' => 'mail2',
                'content' => Studip\VueApp::create('massmail/MassMailMessagesList')
            ],
            [
                'id' => 'cache',
                'name' => 'Cache',
                'icon' => 'admin',
                'content' => Studip\VueApp::create('CacheAdministration')
                    ->withProps([
                        'enabled'       => true,
                        'currentCache'  => StudipDbCache::class,
                        'currentConfig' => StudipDbCache::getConfig(),
                        'cacheTypes'    => CacheType::findAndMapBySQL(
                            fn(CacheType $type) => $type->toArray(),
                            "1 ORDER BY `cache_id`"
                        ),
                    ])
            ],
            [
                'id' => 'colors',
                'name' => 'Farbwähler',
                'icon' => 'colorpicker',
                'content' => Studip\VueApp::create('ColourSelector')
                    ->withProps([
                        'autofocus' => true,
                        'colours' => collect($GLOBALS['PERS_TERMIN_KAT'])->map(
                            fn($data, $id) => ['id' => $id, 'colour' => $data['bgcolor']]
                        )->values()
                    ])
            ]
        ]);
    }

}
