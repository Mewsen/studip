<?php
# Lifter001: TEST
# Lifter002: TODO
# Lifter005: TODO
# Lifter007: TODO
# Lifter003: TODO
# Lifter010: TODO
// +---------------------------------------------------------------------------+
// This file is part of Stud.IP
// StudipSemTreeViewAdmin.class.php
// Class to print out the seminar tree in administration mode
//
// Copyright (c) 2003 André Noack <noack@data-quest.de>
// Suchi & Berg GmbH <info@data-quest.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+

use Studip\Button, Studip\LinkButton;


/**
 * class to print out the seminar tree (admin mode)
 *
 * This class prints out a html representation of the whole or part of the tree
 *
 * @access   public
 * @author   André Noack <noack@data-quest.de>
 */
class StudipSemTreeViewAdmin extends TreeView
{
    public $msg = [];
    public $marked_item;
    public $marked_sem;
    public $mode;
    public $move_item_id;

    /**
     * constructor
     */
    public function __construct($start_item_id = 'root')
    {
        $this->start_item_id = $start_item_id ?: 'root';
        $this->root_content = $GLOBALS['UNI_INFO'];

        parent::__construct('StudipSemTree'); //calling the baseclass constructor

        URLHelper::bindLinkParam('_marked_item', $this->marked_item);

        $this->marked_sem =& $_SESSION['_marked_sem'];
        $this->parseCommand();
    }

    /**
     * manages the session variables used for the open/close thing
     */
    protected function handleOpenRanges()
    {
        $this->open_ranges[$this->start_item_id] = true;

        if (Request::option('close_item') || Request::option('open_item')){
            $toggle_item = (Request::option('close_item')) ? Request::option('close_item') : Request::option('open_item');
            if (!$this->open_items[$toggle_item]){
                $this->openItem($toggle_item);
            } else {
                unset($this->open_items[$toggle_item]);
            }
        }

        if (Request::option('item_id')) {
            $this->anchor = Request::option('item_id');
        }
    }

    public function openItem($item_id)
    {
        if ($this->tree->hasKids($item_id)){
            $this->start_item_id = $item_id;
            $this->open_ranges = null;
            $this->open_items = null;
            $this->open_items[$item_id] = true;
            $this->open_ranges[$item_id] = true;
        } else {
            $this->open_ranges[$this->tree->tree_data[$item_id]['parent_id']] = true;
            $this->open_items[$item_id] = true;
            $this->start_item_id = $this->tree->tree_data[$item_id]['parent_id'];
        }
        if ($this->start_item_id === 'root') {
            $this->open_ranges = null;
            $this->open_ranges[$this->start_item_id] = true;
        }
        $this->anchor = $item_id;
    }

    protected function parseCommand()
    {
        $this->mode = Request::option('mode', $this->mode ?? '');

        if (Request::option('cmd')){
            $exec_func = 'execCommand' . Request::option('cmd');
            if (method_exists($this, $exec_func)) {
                if ($this->$exec_func()) {
                    $this->tree->init();
                }
            }
        }
        if ($this->mode === 'MoveItem' || $this->mode === 'CopyItem') {
            $this->move_item_id = $this->marked_item;
        }
    }

    protected function execCommandOrderItemsAlphabetically()
    {
        $item_id = Request::option('sort_id');
        $sorted_items_stmt = DBManager::get()->prepare(
            'SELECT * FROM sem_tree LEFT JOIN Institute ON studip_object_id = Institut_id WHERE parent_id = :parent_id ORDER BY IF(studip_object_id, Institute.name, sem_tree.name)'
        );
        $sorted_items_stmt->execute([
            'parent_id' => $item_id,
        ]);
        $sorted_items = $sorted_items_stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sorted_items as $priority => $data) {
            $update_priority_stmt = DBManager::get()->prepare('UPDATE sem_tree SET priority = :priority WHERE sem_tree_id = :sem_tree_id');
            $update_priority_stmt->execute([
                'priority' => $priority,
                'sem_tree_id' => $data['sem_tree_id']
            ]);
        }
        $this->msg[$item_id] = 'info§' . _('Die Einträge im Bereich wurden alphabetisch sortiert.');

        return true;
    }

    protected function execCommandOrderItem()
    {
        $direction = Request::option('direction');
        $item_id = Request::option('item_id');
        $items_to_order = $this->tree->getKids($this->tree->tree_data[$item_id]['parent_id']);
        if (!$this->isParentAdmin($item_id) || !$items_to_order) {
            return false;
        }
        for ($i = 0; $i < count($items_to_order); ++$i){
            if ($item_id == $items_to_order[$i]) {
                break;
            }
        }
        if ($direction === 'up' && isset($items_to_order[$i - 1])){
            $items_to_order[$i] = $items_to_order[$i - 1];
            $items_to_order[$i-1] = $item_id;
        } elseif (isset($items_to_order[$i + 1])){
            $items_to_order[$i] = $items_to_order[$i + 1];
            $items_to_order[$i+1] = $item_id;
        }
        for ($i = 0; $i < count($items_to_order); ++$i) {
            $item = StudipStudyArea::find($items_to_order[$i]);
            $item->priority = $i;
            $item->store();
        }
        $this->mode = '';
        $this->msg[$item_id] = 'msg§' . (($direction === 'up') ? _('Element wurde eine Position nach oben verschoben.') : _('Element wurde eine Position nach unten verschoben.'));
        return true;
    }

    protected function execCommandNewItem()
    {
        $item_id = Request::option('item_id');
        if ($this->isItemAdmin($item_id)) {
            $new_item_id = DbView::get_uniqid();
            $this->tree->storeItem($new_item_id, $item_id, _('Neuer Eintrag'), $this->tree->getNumKids($item_id) + 1);
            $this->openItem($new_item_id);
            $this->edit_item_id = $new_item_id;
            if ($this->mode !== 'NewItem') {
                $this->msg[$new_item_id] = 'info§' . _('Hier können Sie die Bezeichnung und die Kurzinformation zu diesem Bereich eingeben.');
            }
            $this->mode = 'NewItem';
        }
        return false;
    }

    protected function execCommandEditItem()
    {
        $item_id = Request::option('item_id');
        if ($this->isItemAdmin($item_id) || $this->isParentAdmin($item_id)){
            $this->mode = "EditItem";
            $this->anchor = $item_id;
            $this->edit_item_id = $item_id;
            if ($this->tree->tree_data[$this->edit_item_id]['studip_object_id']) {
                $this->msg[$item_id] = 'info§' . _('Hier können Sie die Kurzinformation zu diesem Bereich eingeben. Der Name kann nicht geändert werden, da es sich um eine Stud.IP-Einrichtung handelt.');
            } else {
                $this->msg[$item_id] = 'info§' . _('Hier können Sie die Bezeichnung und die Kurzinformation zu diesem Bereich eingeben');
            }
        }
        return false;
    }

    protected function execCommandInsertItem()
    {
        $item_id = Request::option('item_id');
        $parent_id = Request::option('parent_id');
        $item_name = Request::i18n('edit_name');
        $item_info = Request::i18n('edit_info');
        $item_type = Request::int('edit_type');
        if ($this->mode === 'NewItem' && $item_id) {
            if ($this->isItemAdmin($parent_id)){
                $priority = count($this->tree->getKids($parent_id));
                if ($this->tree->InsertItem($item_id, $parent_id, $item_name, $item_info, $priority, null, $item_type)) {
                    $this->mode = '';
                    $this->tree->init();
                    $this->openItem($item_id);
                    $this->msg[$item_id] = 'msg§' . _('Dieser Bereich wurde neu eingefügt.');
                }
            }
        }
        if ($this->mode === 'EditItem') {
            if ($this->isParentAdmin($item_id)){
                if ($this->tree->UpdateItem($item_id, $item_name, $item_info, $item_type)){
                    $this->msg[$item_id] = 'msg§' . _('Bereich wurde geändert.');
                } else {
                    $this->msg[$item_id] = 'info§' . _('Keine Veränderungen vorgenommen.');
                }
                $this->mode = "";
                $this->tree->init();
                $this->openItem($item_id);
            }
        }
        return false;
    }

    protected function execCommandAssertDeleteItem()
    {
        $item_id = Request::option('item_id');
        if ($this->isParentAdmin($item_id)){
            $this->mode = "AssertDeleteItem";
            $this->open_items[$item_id] = true;
            $this->msg[$item_id] = "info§" ._("Sie beabsichtigen diesen Bereich inklusive aller Unterbereiche zu löschen. ")
            . sprintf(_("Es werden insgesamt %s Bereiche gelöscht!"),count($this->tree->getKidsKids($item_id))+1)
            . "<br>" . _("Wollen Sie diese Bereiche wirklich löschen?") . "<br>"
            . LinkButton::createAccept(_('Ja!'),
                    URLHelper::getURL($this->getSelf('cmd=DeleteItem&item_id='.$item_id)),
                    ['title' => _('löschen')])
            . "&nbsp;"
            . LinkButton::createCancel(_('Nein!'),
                    URLHelper::getURL($this->getSelf('cmd=Cancel&item_id='. $item_id)));
        }
        return false;
    }

    protected function execCommandDeleteItem()
    {
        $item_id = Request::option('item_id');
        $item_name = $this->tree->tree_data[$item_id]['name'];
        if ($this->isParentAdmin($item_id) && $this->mode == "AssertDeleteItem"){
            $this->openItem($this->tree->tree_data[$item_id]['parent_id']);
            $items_to_delete = $this->tree->getKidsKids($item_id);
            $items_to_delete[] = $item_id;
            $deleted = $this->tree->DeleteItems($items_to_delete);
            if ($deleted['items']){
                $this->msg[$this->anchor] = "msg§" . sprintf(_("Der Bereich <b>%s</b> und alle Unterbereiche (insgesamt %s) wurden gelöscht. "),htmlReady($item_name),$deleted['items']);
            } else {
                $this->msg[$this->anchor] = "error§" . _("Fehler, es konnten keine Bereiche gelöscht werden !");
            }
            if ($deleted['entries']){
                $this->msg[$this->anchor] .= sprintf(_("<br>Es wurden %s Veranstaltungszuordnungen gelöscht. "),$deleted['entries']);
            }
            $this->mode = "";
        }
        return true;
    }

    protected function execCommandMoveItem()
    {
        $item_id = Request::option('item_id');
        $this->anchor = $item_id;
        $this->marked_item = $item_id;
        $this->mode = 'MoveItem';
        return false;
    }

    protected function execCommandCopyItem()
    {
        $item_id = Request::option('item_id');
        $this->anchor = $item_id;
        $this->marked_item = $item_id;
        $this->mode = 'CopyItem';
        return false;
    }

    protected function execCommandDoMoveItem()
    {
        $item_id = Request::option('item_id');
        $item_to_move = $this->marked_item;
        if (
            $this->mode === 'MoveItem'
            && ($this->isItemAdmin($item_id) || $this->isParentAdmin($item_id))
            && ($item_to_move !== $item_id)
            && ($this->tree->tree_data[$item_to_move]['parent_id'] !== $item_id)
            && !$this->tree->isChildOf($item_to_move, $item_id)
        ) {
            $view = DbView::getView('sem_tree');
            $view->params = [$item_id, count($this->tree->getKids($item_id)), $item_to_move];
            $rs = $view->get_query("view:SEM_TREE_MOVE_ITEM");
            if ($rs->affected_rows()){
                $this->msg[$item_to_move] = "msg§" . _("Bereich wurde verschoben.");
            } else {
                $this->msg[$item_to_move] = "error§" . _("Keine Verschiebung durchgeführt.");
            }
        }
        $this->tree->init();
        $this->openItem($item_to_move);
        $this->mode = "";
        return false;
    }

    protected function execCommandDoCopyItem()
    {
        $item_id = Request::option('item_id');
        $item_to_copy = $this->marked_item;
        if (
            $this->mode === 'CopyItem'
            && ($this->isItemAdmin($item_id) || $this->isParentAdmin($item_id))
            && ($item_to_copy !== $item_id)
            && ($this->tree->tree_data[$item_to_copy]['parent_id'] !== $item_id)
            && !$this->tree->isChildOf($item_to_copy,$item_id)
        ) {
            $items_to_copy = $this->tree->getKidsKids($item_to_copy);
            $seed = DbView::get_uniqid();
            $new_item_id = md5($item_to_copy . $seed);
            $parent_id = $item_id;
            $num_copy = $this->tree->InsertItem(
                $new_item_id,
                $parent_id,
                $this->tree->tree_data[$item_to_copy]['name'],
                $this->tree->tree_data[$item_to_copy]['info'],
                $this->tree->getMaxPriority($parent_id) + 1,
                $this->tree->tree_data[$item_to_copy]['studip_object_id'] ?: null,
                $this->tree->tree_data[$item_to_copy]['type']
            );
            if ($num_copy){
                if ($items_to_copy){
                    for ($i = 0; $i < count($items_to_copy); ++$i) {
                        $num_copy += $this->tree->InsertItem(
                            md5($items_to_copy[$i] . $seed),
                            md5($this->tree->tree_data[$items_to_copy[$i]]['parent_id'] . $seed),
                            $this->tree->tree_data[$items_to_copy[$i]]['name'],
                            $this->tree->tree_data[$items_to_copy[$i]]['info'],
                            $this->tree->tree_data[$items_to_copy[$i]]['priority'],
                            $this->tree->tree_data[$items_to_copy[$i]]['studip_object_id'] ?: null,
                            $this->tree->tree_data[$item_to_copy]['type']
                        );
                    }
                }
                $items_to_copy[] = $item_to_copy;
                for ($i = 0; $i < count($items_to_copy); ++$i){
                    $sem_entries = $this->tree->getSemIds($items_to_copy[$i], false);
                    if ($sem_entries){
                        for ($j = 0; $j < count($sem_entries); ++$j){
                            $num_entries += $this->tree->InsertSemEntry(md5($items_to_copy[$i] . $seed), $sem_entries[$j]);
                        }
                    }
                }
            }

            if ($num_copy){
                $this->msg[$new_item_id] = 'msg§' . sprintf(_('%s Bereich(e) wurde(n) kopiert.'), $num_copy) . '<br>'
                . sprintf(_('%s Veranstaltungszuordnungen wurden kopiert'), $num_entries);
            } else {
                $this->msg[$new_item_id] = 'error§' . _('Keine Kopie durchgeführt.');
            }
            $this->tree->init();
            $this->openItem($new_item_id);
        }
        $this->mode = '';
        return false;
    }

    protected function execCommandInsertFak()
    {
        if ($this->isItemAdmin('root') && Request::option('insert_fak')){
            $item = StudipStudyArea::create([
                'parent_id'        => 'root',
                'priority'         => $this->tree->getNumKids('root') + 1,
                'name'             => '',
                'info'             => '',
                'studip_object_id' => Request::option('insert_fak'),
                'type'             => 0,
            ]);
            if ($item) {
                $this->tree->init();
                $this->openItem($item->id);
                $this->msg[$item->id] = 'msg§' . _('Dieser Bereich wurde neu eingefügt.');
                return false;
            }
        }
        return false;
    }

    protected function execCommandMarkSem()
    {
        $item_id = Request::option('item_id');
        $marked_sem_array =  Request::quotedArray('marked_sem');
        $marked_sem = array_values(array_unique($marked_sem_array));
        $sem_aktion = explode("_",Request::quoted('sem_aktion'));
        if (($sem_aktion[0] === 'mark' || $sem_aktion[1] === 'mark') && count($marked_sem) > 0) {
            $count_mark = 0;
            for ($i = 0; $i < count($marked_sem); ++$i){
                if (!isset($this->marked_sem[$marked_sem[$i]])){
                    ++$count_mark;
                    $this->marked_sem[$marked_sem[$i]] = true;
                }
            }
            if ($count_mark){
                $this->msg[$item_id] = "msg§" . sprintf(_("Es wurde(n) %s Veranstaltung(en) der Merkliste hinzugefügt."),$count_mark);
            }
        }
        if ($this->isItemAdmin($item_id)){
            if (($sem_aktion[0] == 'del' || $sem_aktion[1] == 'del') && count($marked_sem)){
                $not_deleted = [];
                foreach($marked_sem as $key => $seminar_id){
                    $seminar = new Seminar($seminar_id);
                    if(count($seminar->getStudyAreas()) == 1){
                        $not_deleted[] = $seminar->getName();
                        unset($marked_sem[$key]);
                    }
                }
                if ($this->msg[$item_id]){
                    $this->msg[$item_id] .= "<br>";
                } else {
                    $this->msg[$item_id] = "msg§";
                }
                if(count($marked_sem)){
                    $count_del = $this->tree->DeleteSemEntries($item_id, $marked_sem);
                    $this->msg[$item_id] .= sprintf(_("%s Veranstaltungszuordnung(en) wurde(n) aufgehoben."),$count_del);
                }
                if(count($not_deleted)){
                    $this->msg[$item_id] .= '<br>'
                                         . sprintf(_("Für folgende Veranstaltungen wurde die Zuordnung nicht aufgehoben, da es die einzige Zuordnung ist: %s")
, '<br>'.htmlready(join(', ', $not_deleted)));
                }
            }
            $this->anchor = $item_id;
            $this->open_items[$item_id] = true;
            return true;
        }
        return false;
    }

    protected function execCommandCancel()
    {
        $item_id = Request::option('item_id');
        $this->mode = '';
        $this->anchor = $item_id;
        return false;
    }

    public function showSemTree()
    {
        return $this->renderTemplate('sem-tree.php', [
            'start_item_id' => $this->start_item_id,
            'parents'       => $this->tree->getParents($this->start_item_id),
            'tree'          => $this->capture(function () {
                $this->showTree($this->start_item_id);
            }),
        ]);
    }

    /**
     * returns html for the icons in front of the name of the item
     *
     * @param    string  $item_id
     * @return   string
     */
    public function getItemHeadPics($item_id)
    {
        $head = $this->getItemHeadFrontPic($item_id);
        $head .= "\n<td  class=\"printhead\" nowrap  align=\"left\" valign=\"bottom\">";
        if ($this->tree->hasKids($item_id)){
            $head .= Icon::create('folder-full', Icon::ROLE_CLICKABLE, ['title' => !empty($this->open_ranges[$item_id]) ? _('Alle Unterelemente schliessen') : _('Alle Unterelemente öffnen')])->asImg(['class' => 'text-top']);
        } else {
            $head .= Icon::create('folder-empty', 'clickable', ['title' => _('Dieses Element hat keine Unterelemente')])->asImg();
        }
        return $head . "</td>";
    }

    function getItemContent($item_id)
    {
        if ($item_id === $this->edit_item_id) {
            return $this->getEditItemContent();
        }
        if (empty($GLOBALS['SEM_TREE_TYPES'][$this->tree->getValue($item_id, 'type')]['editable'])) {
            $this->msg[$item_id] = "info§" . sprintf(_('Der Typ dieses Elementes verbietet eine Bearbeitung.'));
        }
        if ($item_id === $this->move_item_id){
            $this->msg[$item_id] = 'info§' . sprintf(
                _('Dieses Element wurde zum Verschieben / Kopieren markiert. Bitte wählen Sie ein Einfügesymbol %s aus, um das Element zu verschieben / kopieren.'),
                Icon::create('arr_2right', Icon::ROLE_SORT)->asImg(tooltip2(_('Einfügesymbol')))
            );
        }

        $unassigned_faculties = Institute::findBySQL(
            'LEFT JOIN sem_tree ON (studip_object_id = Institut_id)
             WHERE Institut_id = fakultaets_id
               AND studip_object_id IS NULL'
        );

        return $this->renderTemplate('item-content.php', [
            'item_id'           => $item_id,
            'editable'          => $GLOBALS['SEM_TREE_TYPES'][$this->tree->getValue($item_id, 'type')]['editable'],
            'is_item_admin'     => $this->isItemAdmin($item_id),
            'is_parent_admin'   => $this->isParentAdmin($item_id) && $item_id !== 'root',
            'moving_or_copying' => $this->move_item_id === $item_id && ($this->mode === 'MoveItem' || $this->mode === 'CopyItem'),
            'message'           => $this->getItemMessage($item_id),
            'tree'              => $this->tree,
            'unassigned_faculties' => $unassigned_faculties,
        ]);
    }

    public function getSemDetails($snap, $item_id, $lonely_sem = false)
    {
        $form_name = DbView::get_uniqid();
        $content = "<form class=\"default\" name=\"$form_name\" action=\"" . URLHelper::getLink($this->getSelf("cmd=MarkSem")) ."\" method=\"post\">
        <input type=\"hidden\" name=\"item_id\" value=\"$item_id\">";
        $content .= CSRFProtection::tokenTag();
        $group_by_data = $snap->getGroupedResult("sem_number", "seminar_id");
        $sem_data = $snap->getGroupedResult("seminar_id");
        $group_by_duration = $snap->getGroupedResult("sem_number_end", ["sem_number","seminar_id"]);
        foreach ($group_by_duration as $sem_number_end => $detail){
            if ($sem_number_end != -1 && ($detail['sem_number'][$sem_number_end] && count($detail['sem_number']) == 1)){
                continue;
            } else {
                foreach ($detail['seminar_id'] as $seminar_id => $foo){
                    $start_sem = key($sem_data[$seminar_id]["sem_number"]);
                    if ($sem_number_end == -1){
                        $sem_number_end = count($this->tree->sem_dates)-1;
                    }
                    for ($i = $start_sem; $i <= $sem_number_end; ++$i){
                        if ($group_by_data[$i] && !$tmp_group_by_data[$i]){
                            foreach($group_by_data[$i]['seminar_id'] as $id => $bar){
                                $tmp_group_by_data[$i]['seminar_id'][$id] = key($sem_data[$id]["Name"]);
                            }
                        }
                        $tmp_group_by_data[$i]['seminar_id'][$seminar_id] = key($sem_data[$seminar_id]["Name"]);
                    }
                }
            }
        }
        if (is_array($tmp_group_by_data)){
            foreach ($tmp_group_by_data as $start_sem => $detail){
                $group_by_data[$start_sem] = $detail;
            }
        }

        foreach ($group_by_data as $group_field => $sem_ids){
            foreach ($sem_ids['seminar_id'] as $seminar_id => $foo){
                $name = mb_strtolower(key($sem_data[$seminar_id]["Name"]));
                $name = str_replace("ä","ae",$name);
                $name = str_replace("ö","oe",$name);
                $name = str_replace("ü","ue",$name);
                $group_by_data[$group_field]['seminar_id'][$seminar_id] = $name;
            }
            uasort($group_by_data[$group_field]['seminar_id'], 'strnatcmp');
        }

        krsort($group_by_data, SORT_NUMERIC);

        foreach ($group_by_data as $sem_number => $sem_ids){
            $content .= "\n<tr><td class=\"content_seperator\" colspan=\"3\" style=\"font-size:10pt;\" >" . $this->tree->sem_dates[$sem_number]['name'] . "</td></tr>";
            if (is_array($sem_ids['seminar_id'])){
                foreach(array_keys($sem_ids['seminar_id']) as $seminar_id) {
                    $sem_name = key($sem_data[$seminar_id]["Name"]);
                    $sem_number_start = key($sem_data[$seminar_id]["sem_number"]);
                    $sem_number_end = key($sem_data[$seminar_id]["sem_number_end"]);
                    if ($sem_number_start != $sem_number_end){
                        $sem_name .= " (" . $this->tree->sem_dates[$sem_number_start]['name'] . " - ";
                        $sem_name .= (($sem_number_end == -1) ? _("unbegrenzt") : $this->tree->sem_dates[$sem_number_end]['name']) . ")";
                    }
                    $content .= "<tr><td class=\"table_row_even\" width=\"1%\"><input type=\"checkbox\" name=\"marked_sem[]\" value=\"$seminar_id\" style=\"vertical-align:middle\">
                    </td><td class=\"table_row_even\" style=\"font-size:10pt;\"><a href=\"dispatch.php/course/details/?sem_id=". $seminar_id
                    ."&send_from_search=true&send_from_search_page=" . rawurlencode(URLHelper::getLink($this->getSelf())) . "\">" . htmlReady($sem_name) . "</a>
                    </td><td class=\"table_row_even\" align=\"right\" style=\"font-size:10pt;\">(";
                    $doz_name = array_keys($sem_data[$seminar_id]['doz_name']);
                    $doz_uname = array_keys($sem_data[$seminar_id]['doz_uname']);
                    if (is_array($doz_name)){
                        uasort($doz_name, 'strnatcasecmp');
                        $i = 0;
                        foreach ($doz_name as $index => $value){
                            if ($i == 4){
                                $content .= "... <a href=\"dispatch.php/course/details/?sem_id=". $seminar_id
                                ."&send_from_search=true&send_from_search_page=" . rawurlencode(URLHelper::getLink($this->getSelf())) . "\">("._("mehr").")</a>";
                                break;
                            }
                            $content .= "<a href=\"dispatch.php/profile?username=" . $doz_uname[$index] ."\">" . htmlReady($value) . "</a>";
                            if($i != count($doz_name)-1){
                                $content .= ", ";
                            }
                            ++$i;
                        }
                    }
                    $content .= ") </td></tr>";
                }
            }
        }
        $content .= "<tr><td class=\"table_row_even\" colspan=\"2\">"
            . LinkButton::create(_('Auswählen'), ['title' => _('Auswahl umkehren'), 'onClick' => 'invert_selection(\''. $form_name .'\');return false;'])
            . "</td><td class=\"table_row_even\" align=\"right\"><div class=\"hgroup\">
        <select name=\"sem_aktion\" style=\"margin-right: 1em;\" " . tooltip(_("Aktion auswählen"),true) . ">
        <option value=\"mark\">" . _("in Merkliste übernehmen") . "</option>";
        if (!$lonely_sem && $this->isItemAdmin($item_id)){
            $content .= "<option value=\"del_mark\">" . _("löschen und in Merkliste übernehmen") . "</option>
            <option value=\"del\">" . _("löschen") . "</option>";
        }
        $content .= "</select>" . Button::createAccept(_('OK'), ['title' => _("Gewählte Aktion starten")])
                 . "</div></td></tr> </form>";
        return $content;
    }

    public function getEditItemContent()
    {
        $study_area = StudipStudyArea::find($this->edit_item_id);

        $sem_tree_types = array_filter($GLOBALS['SEM_TREE_TYPES'], function ($type) {
            return $type['editable'];
        });

        return $this->renderTemplate('item-edit.php', [
            'action_url'     => $this->getSelf("cmd=InsertItem&item_id={$study_area->id}"),
            'cancel_url'     => $this->getSelf('cmd=Cancel&item_id=' . ($this->mode === 'NewItem' ? $study_area->parent_id : $study_area->id)),
            'message'        => $this->getItemMessage($study_area->id, 2),
            'study_area'     => $study_area,
            'sem_tree_types' => $sem_tree_types,
            'mode'           => $this->mode,
        ]);
    }

    public function isItemAdmin($item_id)
    {
        if ($GLOBALS['auth']->auth['perm'] === 'root') {
            return true;
        }
        if (!($admin_id = $this->tree->tree_data[$this->tree->getAdminRange($item_id)]['studip_object_id'])){
            return false;
        }
        if (!isset($this->admin_ranges[$admin_id])) {
            $view = DbView::getView('sem_tree');
            $view->params[0] = $auth->auth['uid'];
            $view->params[1] = $admin_id;
            $rs = $view->get_query("view:SEM_TREE_CHECK_PERM");
            $this->admin_ranges[$admin_id] = ($rs->next_record()) ? true : false;
        }
        return (bool) $this->admin_ranges[$admin_id];
    }

    public function isParentAdmin($item_id)
    {
        return $this->isItemAdmin($this->tree->tree_data[$item_id]['parent_id']);
    }

    public function getItemHead($item_id)
    {
        $head = "";
        if (($this->mode == "MoveItem" || $this->mode == "CopyItem") && ($this->isItemAdmin($item_id) || $this->isParentAdmin($item_id))
        && ($this->move_item_id != $item_id) && ($this->tree->tree_data[$this->move_item_id]['parent_id'] != $item_id)
        && !$this->tree->isChildOf($this->move_item_id,$item_id)){
            $head .= "<a href=\"" . URLHelper::getLink($this->getSelf("cmd=Do" . $this->mode . "&item_id=$item_id")) . "\">"
            . Icon::create('arr_2right', 'sort', ['title' => _("An dieser Stelle einfügen")])->asImg(16, ["alt" => _("An dieser Stelle einfügen")])."</a>&nbsp;";
        }
        $head .= parent::getItemHead($item_id);
        if ($item_id != "root"){
            $head .= " (" . $this->tree->getNumEntries($item_id,true) . ") " ;
        }
        if ($item_id != $this->start_item_id && $this->isParentAdmin($item_id) && !empty($this->edit_item_id) && ($item_id != $this->edit_item_id)){
            $head .= "</td><td nowrap align=\"right\" valign=\"bottom\" class=\"printhead\">";
            if (!$this->tree->isFirstKid($item_id)){
                $head .= "<a href=\"". URLHelper::getLink($this->getSelf("cmd=OrderItem&direction=up&item_id=$item_id")) .
                "\">" .  Icon::create('arr_2up', 'sort')->asImg(['class' => 'text-top', 'title' => _("Element nach oben")]) .
                "</a>";
            }
            if (!$this->tree->isLastKid($item_id)){
                $head .= "<a href=\"". URLHelper::getLink($this->getSelf("cmd=OrderItem&direction=down&item_id=$item_id")) .
                "\">" . Icon::create('arr_2down', 'sort')->asImg(['class' => 'text-top', 'title' => _("Element nach unten")]) .
                "</a>";
            }
            $head .= "&nbsp;";
        }
        return $head;
    }

    public function getItemMessage($item_id, $colspan = 1)
    {
        if (empty($this->msg[$item_id])) {
            return '';
        }

        $icons = [
            'error' => Icon::create('decline', Icon::ROLE_ATTENTION),
            'info'  => Icon::create('exclaim', Icon::ROLE_INACTIVE),
            'msg'   => Icon::create('accept', Icon::ROLE_ACCEPT),
        ];

        $msg = explode('Â§', $this->msg[$item_id]);

        return $this->renderTemplate('item-message.php', [
            'colspan' => $colspan,
            'icon'    => $icons[$msg[0]],
            'message' => $msg[1],
        ]);
    }

    public function getSelf($param = '', $with_start_item = true)
    {
        $url_params = "foo=" . DbView::get_uniqid();
        if ($this->mode) {
            $url_params .= '&mode=' . $this->mode;
        }
        if ($with_start_item) {
            $url_params .= '&start_item_id=' . $this->start_item_id;
        }
        if ($param) {
            $url_params .= '&' . $param;
        }
        return parent::getSelf($url_params);
    }

    public function url_for($to = '')
    {
        $args = func_get_args();
        $params = [];
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        $params['foo'] = DbView::get_uniqid();
        if ($this->mode) {
            $params['mode'] = $this->mode;
        }

        return URLHelper::getURL(parent::getSelf(http_build_query($params)));
    }

    public function link_for($to = '')
    {
        return htmlReady(call_user_func_array([$this, 'url_for'], func_get_args()));
    }

    private function renderTemplate($template, array $variables = [])
    {
        $template = $GLOBALS['template_factory']->open("sem_tree/{$template}");
        $template->controller = $this;
        $template->tree_data  = $this->tree->tree_data;
        $template->set_attributes($variables);
        return $template->render();
    }

    private function capture(callable $callable)
    {
        ob_start();
        $callable();
        return ob_get_clean();
    }
}
