<section class="studip-tiles">
    <? foreach ($proposed_studygroups as $course) : ?>
        <a href="<?= URLHelper::getLink('dispatch.php/course/studygroup/details/'.$course->id) ?>">
            <div>
                <?= StudygroupAvatar::getAvatar($course->id)->getImageTag(Avatar::MEDIUM) ?>
                <div>
                    <strong>
                        <?= htmlReady($course->getFullname()) ?>
                    </strong>
                    <div>
                        <?= sprintf(
                                ngettext(
                                    '1 Mitglied',
                                    '%s Mitglieder',
                                    count($course->members)
                                ),
                                count($course->members)
                            ) ?>
                    </div>
                </div>
            </div>
            <? if (count($course->tags)) : ?>
                <div>
                    <? foreach ($course->tags as $tag) : ?>
                        <?= '#'.htmlReady($tag->name) ?>
                    <? endforeach ?>
                </div>
            <? endif ?>
        </a>
    <? endforeach ?>
</section>
