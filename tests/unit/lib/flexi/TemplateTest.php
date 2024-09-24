<?php

use Flexi\Factory;
use Flexi\Template;

final class TemplateTestCase extends \Codeception\Test\Unit
{
    private Factory $factory;

    public function setUp(): void
    {
        $this->factory = $this->make(Factory::class, [
            'open' => $this->make(Template::class),
        ]);
    }

    public function tearDown(): void
    {
        unset($this->factory);
    }

    public function testShouldReturnAPreviouslySetAttribute()
    {
        $template = $this->factory->open('foo');
        $template->set_attribute('whom', 'bar');
        $this->assertEquals('bar', $template->get_attribute('whom'));
    }

    public function testShouldReturnPreviouslySetAttributes()
    {
        $template = $this->factory->open('foo');
        $template->set_attributes(['whom' => 'bar', 'foo' => 'baz']);

        $attributes = $template->get_attributes();
        $this->assertIsArray($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals('bar', $attributes['whom']);
        $this->assertEquals('baz', $attributes['foo']);
    }

    public function testShouldMergeAttributesWithSetAttributes()
    {
        $template = $this->factory->open('foo');
        $template->set_attributes(['a' => 1, 'b' => 2]);

        $this->assertCount(2, $template->get_attributes());
        $this->assertEquals(1, $template->get_attribute('a'));
        $this->assertEquals(2, $template->get_attribute('b'));

        $template->set_attributes(['b' => 8, 'c' => 9]);

        $this->assertCount(3, $template->get_attributes());
        $this->assertEquals(1, $template->get_attribute('a'));
        $this->assertEquals(8, $template->get_attribute('b'));
        $this->assertEquals(9, $template->get_attribute('c'));
    }

    public function testShouldBeEmptyAfterClear()
    {
        $template = $this->factory->open('foo');

        $template->set_attributes(['a' => 1, 'b' => 2]);
        $this->assertNotEmpty($template->get_attributes());

        $template->clear_attributes();
        $this->assertCount(0, $template->get_attributes());
    }
}
