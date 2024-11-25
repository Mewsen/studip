<?php

namespace Studip\Forms;

use MassMail\MassMailMarker;

class SerialWysiwygInput extends WysiwygInput
{

    public function render()
    {
        if (!isset($this->attributes['id'])) {
            $id = md5(uniqid());
            $this->attributes['id'] = $id;
        } else {
            $id = $this->attributes['id'];
        }

        $template = $GLOBALS['template_factory']->open('forms/serial_wysiwyg_input');
        $template->title = $this->title;
        $template->name = $this->name;
        $template->value = $this->value;
        $template->id = $id;
        $template->required = $this->required;
        $template->markers = $this->attributes['markers'];
        $template->attributes = $this->attributes;
        return $template->render();
    }

    public function getRequestValue()
    {
        return \Studip\Markup::purifyHtml(\Request::get($this->name));
    }
}
