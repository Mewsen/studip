<?php
/**
 * @var array $messages
 */
?>

<?= $this->render_partial('enroll/lti/_messages', ['messages' => $messages ?? []]); ?>
