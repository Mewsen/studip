<?php
final class DebugbarController extends Trails_Controller
{
    public function __construct(
        Trails\Dispatcher $dispatcher,
        private readonly DebugBar\DebugBar $debugbar
    ) {
        parent::__construct($dispatcher);
    }

    public function css_action(): void
    {
        $this->set_content_type('text/css;charset=utf-8');
        $this->render_nothing();
        $this->debugbar->getJavascriptRenderer()->dumpCssAssets();
    }

    public function js_action(): void
    {
        $this->set_content_type('text/javascript;charset=utf-8');
        $this->render_nothing();
        $this->debugbar->getJavascriptRenderer()->setIncludeVendors(false)->dumpJsAssets();
    }
}
