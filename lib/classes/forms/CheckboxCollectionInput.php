<?php

namespace Studip\Forms;

class CheckboxCollectionInput extends Input
{
    public function render()
    {
        $template = $GLOBALS['template_factory']->open('forms/checkbox_collection_input');
        $template->title = $this->title;
        $template->name = $this->name;
        $template->selected = $this->value;
        $template->required = $this->required;

        $template->collapsable = $this->attributes['collapsable'] ?? false;
        if (isset($this->attributes['collapsable'])) {
            unset($this->attributes['collapsable']);
        }
        $options = $this->extractOptionsFromAttributes($this->attributes);

        $template->attributes = arrayToHtmlAttributes($this->attributes);
        $template->options = $options;
        return $template->render();
    }
}
