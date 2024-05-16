<?php

use Flexi\Factory;
use Flexi\Template;

final class TemplateEmptyTestCase extends \Codeception\Test\Unit
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

    public function testShouldHaveNoAttributes()
    {
        $template = $this->factory->open('');
        $this->assertCount(0, $template->get_attributes());
    }

    public function testShouldNotBeEmptyAfterSettingAnAttribute()
    {
        $template = $this->factory->open('');
        $template->set_attribute('foo', 'bar');
        $this->assertNotEmpty($template->get_attributes());
    }

    public function testShouldBeEmptyAfterClear()
    {
        $template = $this->factory->open('foo');

        $this->assertEmpty($template->get_attributes());

        $template->clear_attributes();
        $this->assertEmpty($template->get_attributes());
    }
}
