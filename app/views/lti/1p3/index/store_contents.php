<?php
use OAT\Library\Lti1p3Core\Resource\ResourceCollectionInterface;

/**
 * @var Lti_1p3_IndexController $controller
 * @var ResourceCollectionInterface $ltiResources
 */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title data-original="<?= htmlReady(PageLayout::getTitle()) ?>">
            <?= htmlReady(PageLayout::getTitle() . ' - ' . Config::get()->UNI_NAME_CLEAN) ?>
        </title>
        <script>
            window.parent.postMessage({
                type: "LTI_DEEP_LINKING_RESPONSE",
                ltiResources: <?= json_encode($ltiResources->normalize()) ?>
            }, "<?= $GLOBALS['ABSOLUTE_URI_STUDIP'] ?>");
        </script>
    </head>
</html>
