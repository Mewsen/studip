<li class="sem-tree-assigned-<?= $element['id'] ?>">
    <?= htmlReady($element['name']) ?>
    <?php if (empty($values['locked']) && $element['assignable'] && in_array($element['id'], $studyareas ?: [])) : ?>
        <?= Icon::create('trash')->asInput(["name" => 'unassign['.$element['id'].']', "onclick" => "return STUDIP.CourseWizard.unassignNode('".$element['id']."')"]) ?>
    <input type="hidden" name="studyareas[]" value="<?= htmlReady($element['id']) ?>"/>
    <?php endif ?>
    <ul>
        <?php foreach ($element['children'] as $c) : ?>
        <?= $this->render_partial('studyareas/_assigned_node', ['element' => $c]) ?>
        <?php endforeach ?>
    </ul>
</li>
