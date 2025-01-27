<?php

namespace Studip\Forms;

class Fieldset extends Part
{
    protected $legend = null;
    protected bool $collapsable = false;
    protected bool $collapsed = false;

    public function __construct($legend = null)
    {
        $this->legend = $legend;
    }

    public function setLegend($legend)
    {
        $this->legend = $legend;
    }


    public function setCollapsable(bool $state = true): Fieldset
    {
        $this->collapsable = $state;
        return $this;
    }

    public function setCollapsed(bool $state = true): Fieldset
    {
        $this->collapsed = $state;
        return $this;
    }

    public function render()
    {
        $template = $GLOBALS['template_factory']->open('forms/fieldset');
        $template->legend = $this->legend;
        $template->collapsable = $this->collapsable;
        $template->collapsed = $this->collapsable && $this->collapsed;
        $template->parts = $this->parts;
        return $template->render();
    }
}
