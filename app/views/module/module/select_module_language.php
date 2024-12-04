<?php
/**
 * @var Module_ModuleController $controller
 * @var array $languages
 */
?>

<ul class="content-items" style="padding-top: 10px;">
    <? foreach ($languages as $language) : ?>
        <li class="content-item">
            <a class="content-item-link" href="<?= $controller->modulURL(['display_language' => $language['code']]) ?>">
                <div class="content-item-img-wrapper">
                    <?= Assets::img('/images/languages/' . $language['picture'], ['size' => 64]) ?>
                </div>
                <div class="content-item-text">
                    <p class="content-item-title">
                        <?= htmlReady($language['name']) ?>
                    </p>
                    <p class="content-item-description">
                        <? printf(_('Erstellen Sie ein Modul in der Originalsprache <em>%s</em>.'), $language['name']) ?>
                    </p>
                </div>
            </a>
        </li>
    <? endforeach ?>
</ul>
