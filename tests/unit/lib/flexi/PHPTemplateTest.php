<?php

use Flexi\Factory;
use Flexi\TemplateNotFoundException;

final class PhpTemplateTestCase extends Codeception\Test\Unit
{
    private Factory $factory;

    public function setUp(): void
    {
        $this->setUpFS();
        $this->factory = new Factory('var://templates/');
    }


    public function tearDown(): void
    {
        unset($this->factory);

        stream_wrapper_unregister('var');
    }

    public function setUpFS()
    {
        ArrayFileStream::set_filesystem([
            'templates' => [
                'foo_using_partial.php' =>
                    'Hello, <?= $this->render_partial("foos_partial") ?>!',

                'foos_partial.php' =>
                    '<h1><?= $whom ?> at <?= $when ?></h1>',

                'foo_with_partial_collection.php' =>
                    '[<?= $this->render_partial_collection("item", $items, "spacer") ?>]',

                'item.php' =>
                    '"<?= $item ?>"',

                'spacer.php' =>
                    ', ',

                'attributes.php' =>
                    '<? foreach (get_defined_vars() as $name => $value) : ?>' .
                    '<?= $name ?><?= $value ?>' .
                    '<? endforeach ?>',

                'foo.php' =>
                    'Hello, <?= $whom ?>!',

                'layout.php' =>
                    '[<?= $content_for_layout ?>]',
            ]
        ]);
        if (!stream_wrapper_register('var', ArrayFileStream::class)) {
            die('Failed to register protocol');
        }
    }

    public function testRenderPartial()
    {
        $template = $this->factory->open('foo_using_partial');
        $template->set_attribute('whom', 'bar');
        $this->assertEquals(
            'Hello, <h1>bar at now</h1>!',
            $template->render(['when' => 'now'])
        );
    }

    public function testRenderPartialCollection()
    {
        $template = $this->factory->open('foo_with_partial_collection');
        $result = $template->render_partial_collection(
            'item',
            range(1, 3),
            'spacer'
        );
        $this->assertEquals('"1", "2", "3"', $result);
    }

    public function testShouldOverrideAttributesWithThosePassedToRender()
    {
        $template = $this->factory->open('attributes');
        $template->set_attribute('foo', 'baz');

        $template->render(['foo' => 'bar']);
        $this->assertEquals('bar', $template->get_attribute('foo'));

        $template->render();
        $this->assertEquals('bar', $template->get_attribute('foo'));
    }

    public function testRenderWithoutLayout()
    {
        $foo = $this->factory->open('foo');
        $foo->set_attribute('whom', 'bar');
        $this->assertEquals('Hello, bar!', $foo->render());
    }

    public function testRenderWithLayout()
    {
        $foo = $this->factory->open('foo');
        $foo->set_attribute('whom', 'bar');
        $foo->set_layout('layout');
        $out = $foo->render();
        $this->assertEquals('[Hello, bar!]', $out);
    }

    public function testRenderWithLayoutInline()
    {
        $this->assertEquals(
            '[Hello, bar!]',
            $this->factory->render('foo', ['whom' => 'bar'], 'layout')
        );
    }

    public function testRenderWithMissingLayout()
    {
        $foo = $this->factory->open('foo');
        $this->expectException(TemplateNotFoundException::class);
        $foo->set_layout('nosuchlayout');
    }

    public function testRenderWithAttributes()
    {
        $foo = $this->factory->open('foo');
        $foo->set_attribute('whom', 'bar');
        $foo->set_layout('layout');
        $foo_out = $foo->render();

        $bar = $this->factory->open('foo');
        $bar_out = $bar->render(['whom' => 'bar'], 'layout');

        $this->assertEquals($foo_out, $bar_out);
    }
}
