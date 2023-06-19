<script type="text/javascript">
function invert_selection(the_form) {
    my_elements = document.forms[the_form].elements['marked_sem[]'];
    if(!my_elements.length){
        my_elements.checked = !my_elements.checked;
    } else {
        for (i = 0; i < my_elements.length; ++i) {
            my_elements[i].checked = !my_elements[i].checked;
        }
    }
}
</script>
<table width="99%" border="0" cellpadding="0" cellspacing="0">
<? if ($start_item_id !== 'root'): ?>
    <tr>
        <td class="table_row_odd" align="left" valign="top">
            <div style="font-size:10pt; margin-left:10px">
                <strong><?= _('Studienbereiche') ?>:</strong><br>
            <? if ($parents): ?>
                <? foreach (array_reverse($parents) as $parent): ?>
                    &gt;
                    <a class="tree" href="<?= $controller->link_for(['start_item_id' => $parent, 'open_item' => $parent]) ?>">
                        <?= htmlReady($tree_data[$parent]['name']) ?>
                    </a>
                <? endforeach; ?>
            <? endif; ?>
            </div>
        </td>
    </tr>
<? endif; ?>
    <tr>
        <td class="blank" align="left" valign="top">
            <?= $tree ?>
        </td>
    </tr>
</table>
