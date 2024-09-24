<?php

use Flexi\Factory;
use Flexi\Template;

final class TemplateMagicMethodsTestCase extends \Codeception\Test\Unit
{
    private Factory $factory;

    public function setUp(): void
    {
        $this->factory = $this->make(Factory::class, [
            'open' => $this->make(Template::class),
        ]);
        $this->template = $this->factory->open('');
    }

    public function tearDown(): void
    {
        unset($this->factory);
        unset($this->template);
    }

    public function testShouldSetAnAttributeUsingTheMagicMethods()
    {
        $this->template->foo = 'bar';
        $this->assertEquals('bar', $this->template->get_attribute('foo'));
    }

    public function testShouldNotSetAProtectedMemberFieldAsAnAttribute()
    {
        $this->template->layout = 'bar';
        $this->assertEquals('bar', $this->template->layout);
        $this->assertNotEquals('bar', $this->template->get_layout());
    }

    public function testShouldOverwriteAnAttribute()
    {
        $this->template->set_attribute('foo', 'bar');
        $this->template->foo = 'baz';
        $this->assertEquals('baz', $this->template->get_attribute('foo'));
    }

    public function testShouldReturnAnExistingAttributeUsingTheMagicMethods()
    {
        $this->template->set_attribute('foo', 'bar');
        $this->assertEquals('bar', $this->template->foo);
    }

    public function testShouldReturnNullForANonExistingAttributeUsingTheMagicMethods()
    {
        $this->assertNull($this->template->foo);
    }

    public function testShouldUnsetAnAttributeUsingTheMagicMethods()
    {
        $this->template->foo = 'bar';
        unset($this->template->foo);
        $this->assertNull($this->template->foo);
    }

    public function testShouldReturnNullOnUnsettingANonAttribute()
    {
        unset($this->template->foo);
        $this->assertNull($this->template->foo);
    }

    public function testShouldReturnTrueOnIssetForAnAttribute()
    {
        $this->template->foo = 'bar';
        $this->assertTrue(isset($this->template->foo));
    }

    public function testShouldReturnFalseOnIssetForANonExistingAttribute()
    {
        $this->assertFalse(isset($this->template->foo));
    }
}
