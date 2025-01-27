<?php

namespace Studip\Forms;

/**
 * The Text class represents a part of a form that displays a user filter selection.
 */
class UserFilterInput extends Input
{

    public function getValue()
    {
        $value = [];
        foreach ($this->getContextObject()->filters as $connection) {
            $filter = $connection->userfilter;
            $one = [
                'id' => $filter->getId(),
                'attributes' => [
                    'text' => $filter->toString(),
                    'fields' => []
                ]
            ];
            foreach ($filter->getFields() as $field) {
                $one['attributes']['fields'][] = [
                    'id' => $field->getId(),
                    'attributes' => [
                        'type' => get_class($field),
                        'compare-operator' => $field->getCompareOperator(),
                        'value' => $field->getValue()
                    ]
                ];
            }
            $value[] = $one;
        }
        return json_encode($value);
    }

    public function getRequestValue()
    {
        return json_decode(\Request::get($this->name), true);
    }

    public function hasValidation(): bool
    {
        return false;
    }

    public function render(): string
    {
        $template = $GLOBALS['template_factory']->open('forms/user_filter_input');
        $template->title = $this->title;
        $template->name = $this->name;
        $template->value = $this->value;
        $template->id = md5(uniqid());
        $template->required = $this->required;
        $template->attributes = arrayToHtmlAttributes($this->attributes);
        return $template->render();
    }

}
