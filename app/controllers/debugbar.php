<?php
final class DebugbarController extends Trails_Controller
{
    public function css_action(): void
    {
        $this->set_content_type('text/css;charset=utf-8');
        $this->render_nothing();
        app()->get(DebugBar\DebugBar::class)->getJavascriptRenderer()->dumpCssAssets();
    }

    public function js_action(): void
    {
        $this->set_content_type('text/javascript;charset=utf-8');
        $this->render_nothing();
        app()->get(DebugBar\DebugBar::class)->getJavascriptRenderer()->setIncludeVendors(false)->dumpJsAssets();
    }
}
