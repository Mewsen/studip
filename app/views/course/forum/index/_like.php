<?
if (!ForumPerm::has('like_entry', $seminar_id)) return;

$likes = ForumLike::getLikes($topic_id);
shuffle($likes);
?>

<!-- the likes for this post -->
<? if (!empty($likes)) : ?>
    <? // set the current user to the front
    $text = '';
    if (array_search($GLOBALS['user']->id, $likes) !== false) {
        if (sizeof($likes) > 1) {
            $text = '<span class="tooltip">' . sprintf(_('Dir und %s weiteren gefällt das.'), (sizeof($likes) - 1));
            $text .= '<span class="tooltip-content">';
            foreach ($likes as $user_id) {
                if ($user_id != $GLOBALS['user']->id) {
                    $text .= htmlReady(get_fullname($user_id)) .'<br>';
                }
            }
            $text .= '</span></span>';
        } else {
            $text = _('Dir gefällt das.');
        }
    } else {
        $text = '<span class="tooltip">' . sprintf(_('%s gefällt das.'), sizeof($likes));
        $text .= '<span class="tooltip-content">';
        foreach ($likes as $user_id) {
            $text .= htmlReady(get_fullname($user_id)) .'<br>';
        }
        $text .= '</span></span>';
    }

    $text .= ' <br>';
    echo $text;
endif ?>

<!-- like/dislike links -->
<?php $has_liked = in_array($GLOBALS['user']->id, $likes); ?>
<button class="as-link"
        onclick="$.post('<?= $controller->action_link($has_liked ? 'dislike' : 'like', $topic_id) ?>').done(response => $('#like_<?= htmlReady($topic_id) ?>').html(response));return false;"
>
<? if ($has_liked) : ?>
    <?= _('Gefällt mir nicht mehr!'); ?>
<? else: ?>
    <?= _('Gefällt mir!'); ?>
<? endif; ?>
</button>
