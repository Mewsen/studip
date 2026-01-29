<div data-studip-tree>
    <studip-tree start-id="<?= htmlReady($startId) ?>" view-type="table" breadcrumb-icon="institute"
                 :with-search="false" :visible-children-only="false"
                 :editable="true" edit-url="<?= URLHelper::getURL('dispatch.php/admin/tree/edit', [], true) ?>"
                 create-url="<?= URLHelper::getURL('dispatch.php/admin/tree/create', [], true) ?>"
                 delete-url="<?= URLHelper::getURL('dispatch.php/admin/tree/delete', [], true) ?>"
                 :with-courses="true" semester="<?= htmlReady($semester) ?>" :show-structure-as-navigation="true"
                 title="<?= _('Einrichtungshierarchie bearbeiten') ?>"></studip-tree>
</div>
