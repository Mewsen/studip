<div
    id="courseware-shelf-app"
    entry-type="courses"
    entry-id="<?= Context::getId() ?>"
    last-element-id="<?= htmlReady($lastElementId)?>"
    licenses='<?= $licenses ?>'
    feedback-settings='<?= htmlReady($feedback_settings) ?>'
    is-teacher='<?= var_export($isTeacher) ?>'
></div>
