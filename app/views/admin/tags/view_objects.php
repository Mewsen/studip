<?php
/**
 * @var Admin_TagsController $controller
 * @var Tag $tag
 * */
?>
<table class="default">
    <thead>
        <tr>
            <th><?= _('Objekt') ?></th>
            <th><?= _('Typ') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($tag->related_objects as $relation) : ?>
        <tr>
            <td>
                <?
                switch ($relation->range_type) {
                    case 'course':
                        $course = Course::find($relation->range_id);
                        if ($course) {
                            echo '<a href="'.URLHelper::getLink($course->isStudygroup() ? 'dispatch.php/course/studygroup/details/' . $relation->range_id : 'dispatch.php/course/details/index/' . $relation->range_id) . '">';
                            echo htmlReady($course->getFullName());
                            echo '</a>';
                        } else {
                            echo $relation->range_id;
                        }
                        break;
                    default:
                        echo $relation->range_id;
                        break;
                }
                ?>
            </td>
            <td><?
                switch ($relation->range_type) {
                    case 'course':
                        echo _('Veranstaltung');
                        break;
                    default:
                        echo $relation->range_type;
                        break;
                }
                ?></td>
            <td></td>
        </tr>
        <? endforeach ?>
    </tbody>
</table>
