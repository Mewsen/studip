<div data-studip-tree>
    <studip-tree start-id="<?= htmlReady($startId) ?>" view-type="table" breadcrumb-icon="literature"
                 :with-search="false" :visible-children-only="false"
                 :editable="true" edit-url="<?= URLHelper::getURL('dispatch.php/admin/tree/edit', [], true) ?>"
                 create-url="<?= URLHelper::getURL('dispatch.php/admin/tree/create', [], true) ?>"
                 delete-url="<?= URLHelper::getURL('dispatch.php/admin/tree/delete', [], true) ?>"
                 :show-structure-as-navigation="true" :with-course-assign="true"
                 :with-courses="true" semester="<?= htmlReady($semester) ?>"
                 title="<?= _('Veranstaltungshierarchie bearbeiten') ?>"></studip-tree>
</div>
