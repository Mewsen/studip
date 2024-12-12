<?php
/**
 * @var OpenGraphURL $og
 */
?>
<?php
$videofiles = $og->getVideoFiles();
$audiofiles = $og->getAudioFiles();
$og['image'] = filter_var($og['image'], FILTER_VALIDATE_URL) ? $og['image'] : '';
if (Config::get()->LOAD_EXTERNAL_MEDIA === "proxy" && sess()->isCurrentSessionAuthenticated()) {
    $media_url_func = function ($url) {
        return $GLOBALS['ABSOLUTE_URI_STUDIP'] . 'dispatch.php/media_proxy?url=' . urlencode($url);
    };
} elseif (Config::get()->LOAD_EXTERNAL_MEDIA === "deny") {
    $media_url_func = function ($url) {
        return '';
    };
} else {
    $media_url_func = function ($url) {
        return $url;
    };
}
?>
<div class="opengraph <? if (count($videofiles) > 0) echo 'video'; ?> <? if (count($audiofiles) > 0) echo 'audio'; ?>">
<? if ($og['image'] && count($videofiles) === 0): ?>
    <a href="<?= URLHelper::getLink($og['url'], [], true) ?>" class="image"
       target="_blank" rel="noopener noreferrer"
       style="background-image:url(<?= htmlReady($media_url_func($og['image'])) ?>)">
    </a>
<? endif; ?>
    <a href="<?= URLHelper::getLink($og['url'], [], true) ?>" class="info"
       target="_blank" rel="noopener noreferrer">
        <strong><?= htmlReady($og['title']) ?></strong>
    <? if (!count($videofiles)) : ?>
        <p><?= htmlReady($og['description']) ?></p>
    <? endif ?>
    </a>
<? if (count($videofiles)) : ?>
    <div class="video">
        <video width="100%" height="200px" controls>
        <? foreach ($videofiles as $file) : ?>
            <source src="<?= htmlReady($media_url_func($file[0])) ?>"<?= $file[1] ? ' type="'.htmlReady($file[1]).'"' : "" ?>>
        <? endforeach ?>
        </video>
    </div>
<? endif ?>
<? if (count($audiofiles)) : ?>
    <div class="audio">
        <audio width="100%" height="50px" controls>
        <? foreach ($audiofiles as $file) : ?>
            <source src="<?= htmlReady($media_url_func($file[0])) ?>"<?= $file[1] ? ' type="'.htmlReady($file[1]).'"' : "" ?>>
        <? endforeach ?>
        </audio>
    </div>
<? endif ?>
</div>
