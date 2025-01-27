<?php
final class DebugbarController extends StudipController
{
    public function __construct(
        Trails\Dispatcher $dispatcher,
        private readonly DebugBar\DebugBar $debugbar
    ) {
        parent::__construct($dispatcher);
    }

    public function css_action(): void
    {
        $this->response->add_header('Content-Type', 'text/css;charset=utf-8');

        ob_start();
        $this->debugbar->getJavascriptRenderer()->dumpCssAssets();
        $content = ob_get_contents();
        ob_end_clean();
        $this->render_text($content);

    }

    public function js_action(): void
    {
        $this->response->add_header('Content-Type', 'text/javascript;charset=utf-8');
        ob_start();
        $this->debugbar->getJavascriptRenderer()->setIncludeVendors(false)->dumpJsAssets();
        $content = ob_get_contents();
        ob_end_clean();
        $this->render_text($content);
    }
}
