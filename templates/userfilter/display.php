<?php
$i=0;
$fieldText = '';
foreach ($filter->getFields() as $field) {
    if ($i > 0) {
        $fieldText .= ' <b>'._('und').'</b> ';
    }
    $valueNames = $field->getValidValues();
    $ops = $field->getValidCompareOperators();
    $fieldText .= htmlReady($field->getName()." ".$field->getCompareOperatorAsText().
        " " . ($valueNames[$field->getValue()] ?? $field->getValue()));
    $i++;

}
if ($filter->show_user_count) {
    $user_count = count($filter->getUsers());
    $fieldText .= ' (' . sprintf(ngettext('Eine Person', '%s Personen', $user_count), $user_count);
    if ($user_count === 0) {
        $fieldText .= '&nbsp;' . Icon::create('exclaim-circle', Icon::ROLE_ATTENTION)
                ->asSvg(['title' => _('Niemand erfüllt diese Bedingung.')]);
    }
    $fieldText .= ')';
}
echo $fieldText;
