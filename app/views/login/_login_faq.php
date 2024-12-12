<section id="login-faq-content-wrapper">
    <header class="login-box-header">
        <h2><?= htmlReady(Config::get()->LOGIN_FAQ_TITLE) ?></h2>
    </header>
    <div class="login-faq-content">
        <? foreach ($faq_entries as $entry): ?>
            <article class="studip toggle">
                <header>
                    <h1><a href="#"><?= htmlReady($entry->title) ?></a></h1>
                </header>
                <section><?= formatReady($entry->description) ?>
                </section>
            </article>
        <? endforeach ?>
    </div>

</section>

