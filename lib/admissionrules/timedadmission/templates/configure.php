<div id="admission-rule" data-admission-rule="TimedAdmission">
    <timed-admission :start="<?= $startTime ?: time() ?>" :end="<?= $endTime ?: (time() + 3600) ?>"></timed-admission>
</div>
