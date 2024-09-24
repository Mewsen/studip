<?php

use Flexi\Factory;

final class PhpTemplatePartialBugTestCase extends Codeception\Test\Unit
{
    public function setUp(): void
    {
        $this->setUpFS();
        $this->factory = new Factory('var://templates/');
    }

    public function tearDown(): void
    {
        unset($this->factory);

        stream_wrapper_unregister("var");
    }

    public function setUpFS(): void
    {
        ArrayFileStream::set_filesystem([
            'templates' => [
                'layout.php'   =>
                    '<? $do_not_echo_this = $this->render_partial_collection("partial", range(1, 5));' .
                    'echo $content_for_layout;',
                'partial.php'  =>
                    'partial',
                'template.php' =>
                    'template',
            ]
        ]);
        if (!stream_wrapper_register('var', ArrayFileStream::class)) {
            die('Failed to register protocol');
        }
    }

    public function testPartialBug()
    {
        $template = $this->factory->open('template');
        $template->set_layout('layout');
        $result = $template->render();
        $this->assertEquals($result, "template");
    }
}
