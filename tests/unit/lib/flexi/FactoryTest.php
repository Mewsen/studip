<?php

use Flexi\Factory;
use Flexi\TemplateNotFoundException;
use Flexi\PhpTemplate;

final class FactoryTestCase extends \Codeception\Test\Unit
{
    private Factory $factory;

    public function setUp(): void
    {
        $this->setUpFS();

        $this->factory = new Factory('var://templates');
    }

    public function tearDown(): void
    {
        unset($this->factory);
        stream_wrapper_unregister('var');
    }

    public function setUpFS(): void
    {
        ArrayFileStream::set_filesystem([
            'templates' => [
                'foo.php'           => 'some content',
                'baz.unknown'       => 'some content',
                'multiplebasenames' => [
                    'foo.txt' => 'there is no matching template class',
                    'foo.php' => 'some content',
                    'bar.txt' => 'there is no matching template class',
                ],
                'baz.known-ext' => 'some content',
            ],
        ]);
        if (!stream_wrapper_register('var', ArrayFileStream::class)) {
            die('Failed to register protocol');
        }
    }

    public function testShouldCreateFactory()
    {
        $factory = new Factory('.');
        $this->assertNotNull($factory);
    }

    public function testShouldCreateFactoryUsingPath()
    {
        $path = 'var://';
        $factory = new Factory($path);
        $this->assertNotNull($factory);
    }

    public function testShouldOpenTemplateUsingRelativePath()
    {
        $foo = $this->factory->open('foo');
        $this->assertNotNull($foo);
    }

    public function testShouldOpenTemplateUsingAbsolutePath()
    {
        $foo = $this->factory->open('var://templates/foo');
        $this->assertNotNull($foo);
    }

    public function testShouldThrowAnExceptionOpeningAMissingTemplateWithoutFileExtension()
    {
        $this->expectException(TemplateNotFoundException::class);
        $this->factory->open('bar');
    }

    public function testShouldThrowAnExceptionOpeningAMissingTemplateWithFileExtension()
    {
        $this->expectException(TemplateNotFoundException::class);
        $this->factory->open('bar.php');
    }

    public function testShouldOpenTemplateUsingExtension()
    {
        $this->assertInstanceOf(
            PhpTemplate::class,
            $this->factory->open('foo.php')
        );
    }

    public function testShouldThrowAnExceptionWhenOpeningATemplateWithUnknownExtension()
    {
        $this->expectException(TemplateNotFoundException::class);
        $this->factory->open('baz');
    }

    public function testShouldThrowAnExceptionOpeningATemplateInANonExistingDirectory()
    {
        $this->expectException(TemplateNotFoundException::class);
        $this->factory->open('doesnotexist/foo');
    }

    public function testShouldSearchForASupportedTemplate()
    {
        $this->assertInstanceOf(
            PhpTemplate::class,
            $this->factory->open('multiplebasenames/foo')
        );
    }

    public function testShouldRespondToAddedHandlers()
    {
        $handler = new class('', $this->factory) extends Flexi\Template {
            public function _render(): string
            {
                return '';
            }
        };
        $this->factory->add_handler('known-ext', $handler::class);
        $this->factory->open('baz.known-ext');
    }
}
