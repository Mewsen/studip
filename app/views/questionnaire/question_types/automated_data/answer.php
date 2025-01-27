<?
/**
 * @var QuestionnaireQuestion $question
 */
$data = [];
$user = User::findCurrent();
if ($question['questiondata']['geschlecht']) {
    $map = [
        0 => _('unbekannt'),
        1 => _('männlich'),
        2 => _('weiblich'),
        3 => _('divers')
    ];
    $data[] = _('Geschlecht:') . ' ' . $map[$user['geschlecht']];
}
if ($question['questiondata']['studienfach']) {
    $studienfach = [];
    foreach ($user->studycourses as $studycourse) {
        $studienfach[] = $studycourse->studycourse_name;
    }
    $data[] = _('Studienfach:') . ' ' . (implode(', ', $studienfach) ?: _('Kein Eintrag'));
}
if ($question['questiondata']['studiengang']) {
    $studiengang = [];
    foreach ($user->studycourses as $studycourse) {
        $studiengang[] = $studycourse->studycourse_name . ' ' . $studycourse->degree_name;
    }
    $data[] = _('Studiengang:') . ' ' . (implode(', ', $studiengang) ?: _('Kein Eintrag'));
}
if ($question['questiondata']['studiengangfachsemester']) {
    $studiengang = [];
    foreach ($user->studycourses as $studycourse) {
        $studiengang[] = $studycourse->studycourse_name . ' ' . $studycourse->degree_name . ' ' . $studycourse->semester;
    }
    $data[] = _('Studiengang und Fachsemester:') . ' ' . (implode(', ', $studiengang) ?: _('Kein Eintrag'));
}
if (isset($question['questiondata']['datafields'])) {
    foreach ($question['questiondata']['datafields'] as $datafield_id) {
        $datafield = DataField::find($datafield_id);
        if ($datafield) {
            $entry = DatafieldEntryModel::findOneBySQL('range_id = :user_id AND datafield_id = :datafield_id', [
                'user_id' => $user->getId(),
                'datafield_id' => $datafield_id
            ]);
            $data[] = $datafield['name'] . ': ' . ($entry ? $entry['content'] : _('Kein Eintrag'));
        }
    }
}
?>
<? if ($question->questionnaire['anonymous']) : ?>
    <?= MessageBox::info(_('Die folgenden Daten werden in diesem Fragebogen automatisch erfasst. Die Teilnahme an diesem Fragebogen erfolgt grundsätzlich anonym, aber eventuell können die automatisch erfassten Daten zu einer Deanonymisierung führen.'), array_map('htmlReady', $data)) ?>
<? else : ?>
    <?= MessageBox::info(_('Die folgenden Daten werden in diesem Fragebogen automatisch erfasst:'), array_map('htmlReady', $data)) ?>
<? endif ?>
