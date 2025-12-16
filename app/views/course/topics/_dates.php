<ul class="clean">
    <? foreach ($topic->dates as $date) : ?>
        <li>
            <a href="<?= URLHelper::getLink('dispatch.php/course/dates/details/' . $date->id) ?>"
               data-dialog="size=auto"
               style="white-space: nowrap"
           >
                <?= Icon::create('date')->asSvg(['class' => 'text-bottom']) ?>
                <?= htmlReady($date->getFullName()) ?>
            </a>
        </li>
    <? endforeach ?>
</ul>
