<?php

class Admin_TagsController extends AuthenticatedController
{
    /**
     * Common tasks for all actions.
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $GLOBALS['perm']->check('root');
        Navigation::activateItem('/admin/locations/tags');
        PageLayout::setTitle(_('Schlagwortverwaltung'));
    }

    public function index_action()
    {
        Tag::deleteBySQL('LEFT JOIN `tags_relations` ON (`tags`.`id` = `tags_relations`.`tag_id`) WHERE `tags_relations`.`range_id` IS NULL');
        $this->page = Request::int('page', 0);
        $this->tags = Tag::findBySQL('1 ORDER BY `name` ASC LIMIT :offset, :limit', [
            'offset' => $this->page * Config::get()->ENTRIES_PER_PAGE,
            'limit' => Config::get()->ENTRIES_PER_PAGE
        ]);
        $this->all_tags = Tag::countBySql('1');
    }

    public function edit_action(Tag $tag)
    {
        PageLayout::setTitle(sprintf(_('Schlagwort „%s“ bearbeiten'), $tag->name));
        $form = \Studip\Forms\Form::fromSORM(
            $tag,
            [
                'legend' => _('Grunddaten'),
                'fields' => [
                    'name' => [
                        'label' =>_('Name'),
                        'validate' => function ($value) use ($tag) {
                            $output = '';
                            if ($value !== mb_strtolower($value)) {
                                $output .= _('Schlagwörter sollen keine Großbuchstaben entahlten').' ';
                            }
                            foreach (['\n', '#', '|', ' '] as $forbidden) {
                                if (str_contains($value, $forbidden)) {
                                    $output .= _('Schlagwörter dürfen keine Zeilenumbrüche, Leerzeichen, Doppelkreuze (#) oder Pipe-Zeichen (|) enthalten.').' ';
                                    break;
                                }
                            }
                            if (Tag::findOneByName($value) && $value !== $tag->name) {
                                $output .= _('Dieses Schlagwort ist schon vergeben.').' ';
                            }
                            return $output !== '' ? $output : true;
                        }
                    ],
                    'active' => _('Aktiv')
                ]
            ]
        )->autoStore()->setURL($this->indexURL());
        $this->render_form($form);
    }

    public function view_objects_action(Tag $tag)
    {
        $this->tag = $tag;
        PageLayout::setTitle(sprintf(_("Verknüpfte Objekte mit Schlagwort „%s“"), $tag->name));
    }
}
