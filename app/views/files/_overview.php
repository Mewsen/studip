<?php
/**
 * @var FilesController $controller
 * @var array $vue_topfolder
 * @var bool $show_download_column
 * @var array $all_files
 * @var int $all_files_c
 * @var array $uploaded_files
 * @var int $uploaded_files_c
 * @var array $public_files
 * @var int $public_files_c
 * @var array $uploaded_unlic_files
 * @var int   $uploaded_unlic_files_c
 * @var bool|null $no_files
 */
?>
<form class="default" method="get" action="<?= $controller->link_for('files_dashboard/search') ?>">
    <?= $this->render_partial('files_dashboard/_input-group-search') ?>
</form>

<? if ($all_files) : ?>
    <?
    $tfoot_link = [
        'text' => sprintf(
            ngettext('Insgesamt %d Datei', 'Insgesamt %d Dateien', $all_files_c),
            $all_files_c
        ),
        'href' => $controller->link_for('files/overview', ['view' => 'all_files'])
    ];
    ?>
    <form method="post">
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\VueApp::create('FilesTable')
            ->withProps([
                'enable_table_filter' => false,
                'files'               => $all_files,
                'show_bulk_actions'   => false,
                'showdownloads'       => $show_download_column,
                'table_title'         => _('Alle Dateien'),
                'tfoot_link'          => $tfoot_link,
                'topfolder'           => $vue_topfolder,
            ]) ?>
    </form>
<? endif ?>

<? if ($uploaded_files) : ?>
    <?
    $tfoot_link = [
        'text' => sprintf(
            ngettext('Insgesamt %d Datei', 'Insgesamt %d Dateien', $uploaded_files_c),
            $uploaded_files_c
        ),
        'href' => $controller->link_for('files/overview', ['view' => 'my_uploaded_files'])
    ];
    ?>
    <form method="post">
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\VueApp::create('FilesTable')
            ->withProps([
                'enable_table_filter' => false,
                'files'               => $uploaded_files,
                'show_bulk_actions'   => false,
                'showdownloads'       => $show_download_column,
                'table_title'         => _('Persönlicher Dateibereich'),
                'tfoot_link'          => $tfoot_link,
                'topfolder'           => $vue_topfolder,
            ]) ?>
    </form>
<? endif ?>

<? if ($public_files) : ?>
    <?
    $tfoot_link = [
        'text' => sprintf(
            ngettext('Insgesamt %d Datei', 'Insgesamt %d Dateien', $public_files_c),
            $public_files_c
        ),
        'href' => $controller->link_for('files/overview', ['view' => 'my_public_files'])
    ];
    ?>
    <form method="post">
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\VueApp::create('FilesTable')
            ->withProps([
                'enable_table_filter' => false,
                'files'               => $public_files,
                'show_bulk_actions'   => false,
                'showdownloads'       => $show_download_column,
                'table_title'         => _('Meine öffentlichen Dateien'),
                'tfoot_link'          => $tfoot_link,
                'topfolder'           => $vue_topfolder,
            ]) ?>
    </form>
<? endif ?>

<? if ($uploaded_unlic_files) : ?>
    <?
    $tfoot_link = [
        'text' => sprintf(
            ngettext('Insgesamt %d Datei', 'Insgesamt %d Dateien', $uploaded_unlic_files_c),
            $uploaded_unlic_files_c
        ),
        'href' => $controller->link_for('files/overview', ['view' => 'my_uploaded_files_unknown_license'])
    ];
    ?>
    <form method="post">
        <?= CSRFProtection::tokenTag() ?>
        <?= Studip\VueApp::create('FilesTable')
            ->withProps([
                'enable_table_filter' => false,
                'files'               => $uploaded_unlic_files,
                'show_bulk_actions'   => false,
                'showdownloads'       => $show_download_column,
                'table_title'         => _('Meine Dateien mit ungeklärter Lizenz'),
                'tfoot_link'          => $tfoot_link,
                'topfolder'           => $vue_topfolder,
            ]) ?>
    </form>
<? endif ?>

<? if (!empty($no_files)) : ?>
    <?= MessageBox::info(_('Es sind keine Dateien vorhanden, die für Sie zugänglich sind!')) ?>
<? endif ?>
