<?php
/**
 * @var Enroll_LtiController $controller
 * @var int $provisioningMode
 * @var string $callbackId
 */

use Lti\Enum\UserProvisioningMode;

?>

<?php
/**
 * @var array $messages
 */
?>

<?= $this->render_partial('enroll/lti/_messages', ['messages' => $messages ?? []]); ?>

<? if(empty($messages)): ?>
<div class="provisioning-modes">
    <h1><?= _('Willkommen!') ?></h1>
    <?= MessageBox::info(_('Es sieht so aus, als wäre dies Ihr erstes Mal hier. Bitte wählen Sie eine der folgenden Kontooptionen aus.')) ?>
    <br />
    <ul class="studip-card-container">

        <li class="studip-card">
            <header class="studip-card__header">
                <p class="studip-card__title">
                    <?= _('Ich habe bereits ein Konto') ?>
                </p>
            </header>
            <div class="studip-card__body">
                <?= Icon::create('role') ?>
                <p class="studip-card__subtitle"><?= _('Bestehendes Konto verwenden') ?></p>
                <p class="studip-card__description">
                    <?= _('Melde dich an, um dein bestehendes Konto zu verknüpfen.') ?>
                </p>
            </div>
            <footer class="studip-card__footer">
                <a href="<?= URLHelper::getLink('dispatch.php/login?callback_id=' . $callbackId) ?>" class="button">
                    <?= _('Anmelden') ?>
                </a>
            </footer>
        </li>

        <? if($provisioningMode !== UserProvisioningMode::ExistingAccountsOnly->value): ?>
        <li class="studip-card">
            <header class="studip-card__header">
                <p class="studip-card__title">
                    <?= _('Ich möchte ein neues Konto erstellen') ?>
                </p>
            </header>
            <div class="studip-card__body">
                <?= Icon::create('add') ?>
                <p class="studip-card__subtitle"><?= _('Konto erstellen') ?></p>
                <p class="studip-card__description">
                    <?= _('Mit einem neuen Konto starten') ?>
                </p>
            </div>
            <footer class="studip-card__footer">
                <form action="<?= $controller->url_for('enroll/lti/create_new_account') ?>" method="post">
                    <?= CSRFProtection::tokenTag() ?>
                    <input type="hidden" name="callback_id" value="<?= $callbackId ?>" />
                    <button type="submit" class="button">
                        <?= _('Ein neues Konto erstellen') ?>
                    </button>
                </form>
            </footer>
        </li>
        <? endif ?>
    </ul>
</div>
<? endif ?>
