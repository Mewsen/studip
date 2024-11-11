<?php
# Lifter010: TODO

/**
 * @var Admin_WebserviceAccessController $controller
 * @var WebserviceAccessRule[] $ws_rules
 */
?>
<h3><?=_("Liste der Zugriffsregeln")?></h3>
<form action="<?=$controller->url_for('admin/webservice_access/update#edit')?>" method="post" class="default">
<?=CSRFProtection::tokenTag()?>
<table class="default">
<tr>
  <th style="width: 30%;">
    <?= _('API-Key') ?>
  </th>
  <th style="width: 30%;">
    <?= _('Methode') ?>
  </th>
  <th style="width: 30%;">
    <?= _('IP Bereich') ?>
  </th>
  <th style="width: 5%;">
    <?= _('Typ') ?>
  </th>
  <th style="width: 5%;">
    <?= _('Aktion') ?>
  </th>
</tr>
<? foreach ($ws_rules as $rule): ?>
  <tr>
    <? if (isset($edit) && $edit == $rule->id) :?>
        <td>
            <a name="edit"></a>
            <input name="ws_rule_id" type="hidden" value="<?=$rule->id?>">
            <input name="ws_rule_api_key" style="width:90%" type="text" required value="<?= htmlReady($rule->api_key) ?>">
        </td>
        <td>
            <input name="ws_rule_method" style="width:90%" type="text" value="<?= htmlReady($rule->method) ?>">
        </td>
        <td>
            <input name="ws_rule_ip_range" style="width:90%" type="text" value="<?= htmlReady($rule->ip_range) ?>">
        </td>
        <td>
            <select name="ws_rule_type">
            <option <?=($rule->type == 'allow' ? 'selected' : '') ?>>allow</option>
            <option <?=($rule->type == 'deny' ? 'selected' : '') ?>>deny</option>
            </select>
        </td>
        <td>
            <?= Icon::create('accept', Icon::ROLE_ACCEPT)->asInput([
                'title' => _('Änderungen speichern'),
                'type'  => 'image',
                'class' => 'middle',
                'name'  => 'ok',
            ]) ?>
            <?= Icon::create('decline', Icon::ROLE_ATTENTION)->asInput([
                'title' => _('Abbrechen'),
                'type'  => 'image',
                'class' => 'middle',
                'name' => 'cancel',
            ]) ?>
        </td>
    <? else : ?>
        <td>
            <?= htmlReady($rule->api_key) ?>
        </td>
        <td>
            <?= htmlReady($rule->method) ?>
        </td>
        <td>
            <?= htmlReady($rule->ip_range) ?>
        </td>
        <td>
            <?= htmlReady($rule->type) ?>
        </td>
        <td>
          <a href="<?= $controller->url_for('admin/webservice_access/edit/'.$rule->id.'#edit') ?>">
            <?= Icon::create('edit')->asImg(['title' => _('bearbeiten')]) ?>
          </a>
          <a href="<?= $controller->url_for('admin/webservice_access/delete/'.$rule->id) ?>">
              <?= Icon::create('trash')->asImg(['title' => _('löschen')]) ?>
          </a>
        </td>
    <? endif;?>
  </tr>
<? endforeach ?>
</table>
</form>
<?
$sidebar = Sidebar::Get();

$actions = new ActionsWidget();
$actions->addLink(
    _('Regeln testen'),
    $controller->url_for('admin/webservice_access/test'),
    Icon::create('unit-test')
);
$actions->addLink(
    _('Neue Zugriffsregel anlegen'),
    $controller->url_for('admin/webservice_access/new'),
    Icon::create('add')
);

$sidebar->addWidget($actions);
