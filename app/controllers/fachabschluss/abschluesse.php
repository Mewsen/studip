<?php
/**
 * @author      Peter Thienel <thienel@data-quest.de>
 * @license     GPL2 or any later version
 * @since       3.5
 */

class Fachabschluss_AbschluesseController extends MVVController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        // set navigation
        Navigation::activateItem($this->me . '/fachabschluss/abschluesse');
        $this->action = $action;
    }

    /**
     * Shows list of Abschluesse
     */
    public function index_action()
    {
        PageLayout::setTitle(_('Verwaltung der Abschlüsse'));
        $this->initPageParams();
        $filter = ['mvv_fach_inst.institut_id' => MvvPerm::getOwnInstitutes()];
        $this->sortby = $this->sortby ?: 'name';
        $this->order = $this->order ?: 'ASC';
        //get data
        $this->abschluesse = self::getAllEnriched(
            $this->sortby,
            $this->order,
            self::$items_per_page,
            self::$items_per_page * ($this->page - 1),
            $filter
        );
        if (count($this->abschluesse) === 0) {
            PageLayout::postInfo(_('Es wurden noch keine Abschlüsse angelegt.'));
        }
        $this->count = self::getCount($filter);

        $this->setSidebar();

        $helpbar = Helpbar::get();
        $widget = new HelpbarWidget();
        $widget->addElement(new WidgetElement(_('Auf diesen Seiten können Sie Fächer und Abschlüsse verwalten.').'</br>'));
        $widget->addElement(new WidgetElement(_('Ein Abschluss kann aufgeklappt werden, um die Fächer anzuzeigen, die diesem Abschluss bereits zugeordnet wurden.')));
        $helpbar->addWidget($widget);
    }

    public function details_action($abschluss_id = null)
    {
        $this->abschluss = Abschluss::get($abschluss_id);
        $this->abschluss_id = $this->abschluss->id;
        $this->perm_institutes = MvvPerm::getOwnInstitutes();
        if (!Request::isXhr()){
            $this->perform_relayed('index');
            return;
        }
    }

    /**
     * Edits the selected Abschluss
     *
     * @param $abschluss_id
     */
    public function abschluss_action($abschluss_id = null)
    {
        $this->abschluss_kategorien = AbschlussKategorie::getAll();
        if (count($this->abschluss_kategorien) === 0) {
            PageLayout::postError(
                _('Es wurden noch keine Abschluss-Kategorien angelegt. '
                . 'Bevor Sie fortfahren, legen Sie bitte hier zunächst eine Abschluss-Kategorie an!')
            );
            $this->redirect('fachabschluss/kategorien/kategorie');
        }
        $this->abschluss = new Abschluss($abschluss_id);
        if ($this->abschluss->isNew()) {
            PageLayout::setTitle(_('Neuen Abschluss anlegen'));
            $success_message = _('Der Abschluss "%s" wurde angelegt.');
        } else {
            PageLayout::setTitle(sprintf(
                _('Abschluss: %s bearbeiten'),
                $this->abschluss->getDisplayName()
            ));
            $success_message = _('Der Abschluss "%s" wurde geändert.');
        }
        if (Request::submitted('store')) {
            CSRFProtection::verifyUnsafeRequest();
            $store = true;
            $this->abschluss->name = Request::i18n('name')->trim();
            $this->abschluss->name_kurz = Request::i18n('name_kurz')->trim();
            $this->abschluss->beschreibung = Request::i18n('beschreibung')->trim();
            if (!$this->assignKategorie($this->abschluss, Request::option('kategorie_id'))) {
                PageLayout::postError(_('Es muss eine Abschluss-Kategorie ausgewählt werden.'));
                $store = false;
            }
            if (Abschluss::findByName($this->abschluss->name)) {
                sprintf(_('Es existiert bereits ein Abschluss mit dem Namen "%s"!'), $this->name);
                $store = false;
            }
            if ($store) {
                $stored = $this->abschluss->store(true);
            }
            if ($stored !== false) {
                $this->sessSet('sortby', 'chdate');
                $this->sessSet('order', 'DESC');
                if ($stored) {
                    PageLayout::postSuccess(sprintf(
                        $success_message,
                        htmlReady($this->abschluss->name)
                    ));
                } else {
                    PageLayout::postInfo(_('Es wurden keine Änderungen vorgenommen.'));
                }
                $this->redirect($this->indexURL());
                return;
            }
        }

        $this->setSidebar();
        if (!$this->abschluss->isNew()) {
            $sidebar = Sidebar::get();
            $action_widget = $sidebar->getWidget('actions');
            $action_widget->addLink(
                _('Log-Einträge dieses Abschlusses'),
                $this->url_for('shared/log_event/show/Abschluss/' . $this->abschluss->id),
                Icon::create('log')
            )->asDialog();
        }
    }

    /**
     * Deletes the Abschluss
     */
    public function delete_action($abschluss_id)
    {
        $abschluss = Abschluss::get($abschluss_id);
        if (Request::submitted('delete')) {
            if ($abschluss->isNew()) {
                PageLayout::postError(_('Der Abschluss kann nicht gelöscht werden (unbekannter Abschluss).'));
            } else {
                CSRFProtection::verifyUnsafeRequest();
                if (count($abschluss->studiengaenge)) {
                    $sp = ngettext('Studiengang', 'Studiengängen', count($abschluss->studiengaenge));
                    PageLayout::postError(sprintf(
                        _('Der Abschluss kann nicht gelöscht werden (in %s %s verwendet).'),
                        count($abschluss->studiengaenge), $sp
                    ));
                } else {
                    $name = $abschluss->name;
                    $abschluss->delete();
                    PageLayout::postSuccess(sprintf(
                        _('Der Abschluss "%s" wurde gelöscht.'),
                        htmlReady($name)
                    ));
                }

            }
        }
        $this->redirect($this->indexURL());
    }

    public function fach_action($fach_id = null)
    {
        $response = $this->relay('fachabschluss/faecher/fach/' . $fach_id);
        if (Request::isXhr()) {
            $this->render_text($response->body);
        } else {
            if ($response->headers['Location']) {
                $this->redirect($response->headers['Location']);
            } else {
              $this->relocate('fachabschluss/faecher/fach/' . $fach_id);
            }
        }
    }

    /**
     * Creates the sidebar widgets
     */
    protected function setSidebar()
    {
        if (MvvPerm::havePermCreate('Abschluss')) {
            $sidebar = Sidebar::get();

            $widget = new ActionsWidget();
            $widget->addLink(
                _('Neuen Abschluss anlegen'),
                $this->abschlussURL(),
                Icon::create('add'),
                ['data-dialog' => '']
            );
            $sidebar->addWidget($widget);
        }
        $this->sidebar_rendered = true;
    }

    /**
     * Returns all or a specified (by row count and offset) number of
     * Abschluesse sorted and filtered by given parameters and enriched with
     * some additional fields. This function is mainly used in the list view.
     *
     * @param string $sortby Field name to order by.
     * @param string $order ASC or DESC direction of order.
     * @param int $row_count The max number of objects to return.
     * @param int $offset The first object to return in a result set.
     * @param array $filter Key-value pairs of filed names and values
     * to filter the result set.
     * @return SimpleCollection A SimpleCollection of Abschluss objects.
     */
    private static function getAllEnriched(
        $order_field = 'name',
        $order = 'ASC',
        $row_count = null,
        $offset = null,
        $filter = null
    ): SimpleCollection
    {
        $order_by = Abschluss::createSortStatement($order_field, $order, 'chdate',
            ['kategorie_name', 'count_faecher', 'count_studiengaenge']);
        return Abschluss::getEnrichedByQuery('
                SELECT abschluss.*, mvv_abschl_kategorie.name AS `kategorie_name`,
                    COUNT(DISTINCT mvv_stgteil.fach_id) AS `count_faecher`,
                    COUNT(DISTINCT mvv_studiengang.studiengang_id) AS `count_studiengaenge`
                FROM abschluss
                    LEFT JOIN mvv_abschl_zuord USING (abschluss_id)
                    LEFT JOIN mvv_abschl_kategorie USING (kategorie_id)
                    LEFT JOIN mvv_studiengang USING (abschluss_id)
                    LEFT JOIN mvv_stg_stgteil USING (studiengang_id)
                    LEFT JOIN mvv_stgteil USING (stgteil_id)
                    LEFT JOIN mvv_fach_inst USING (fach_id)
                ' . Abschluss::getFilterSql($filter, true) . '
                GROUP BY abschluss_id
                ORDER BY ' . $order_by,
            [], $row_count, $offset);
    }

    /**
     * Returns the number of Abschlüsse optional filtered by $filter.
     *
     * @param array $filter Key-value pairs of filed names and values
     * to filter the result set.
     * @return int The number of Abschluesse
     */
    public static function getCount($filter = null)
    {
        $query = '
            SELECT COUNT(DISTINCT(abschluss_id))
            FROM abschluss
                LEFT JOIN mvv_abschl_zuord USING (abschluss_id)
                LEFT JOIN mvv_abschl_kategorie USING (kategorie_id)
                LEFT JOIN mvv_studiengang USING (abschluss_id)
                LEFT JOIN mvv_stg_stgteil USING (studiengang_id)
                LEFT JOIN mvv_stgteil USING (stgteil_id)
                LEFT JOIN mvv_fach_inst USING (fach_id)
                ' . Abschluss::getFilterSql($filter, true);
        $db = DBManager::get()->prepare($query);
        $db->execute();
        return $db->fetchColumn(0);
    }

    /**
     * Assigns an Abschluss-Kategorie to this Abschluss.
     *
     * @param string $kategorie_id The id of the Abschluss-Kategorie
     * @param int Position of this Abschluss in the given Kategorie.
     * @return object|null The assigned Kategorie. Null if assigned
     * Abschluss-Kategorie is unknown
     * TODO Position?
     */
    private static function assignKategorie($abschluss, $kategorie_id, $position = null)
    {
        $kategorie = AbschlussKategorie::find($kategorie_id);
        if ($kategorie) {
            $category_assignment = new AbschlussZuord($abschluss->id);
            $category_assignment->kategorie_id = $kategorie->id;
            if (!is_null($position)) {
                $category_assignment->position = $position;
            }
            $abschluss->category_assignment = $category_assignment;
        }
        return $kategorie;
    }

}
