<?php
class HelpbarWidget extends Widget
{
    public $icon = false;
    protected $layout = false;

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
}
