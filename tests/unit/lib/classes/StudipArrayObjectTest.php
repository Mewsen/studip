<?php
/**
 * @author   Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license  GPL2 or any later version
 *
 * @covers StudipArrayObject
 */
class StudipArrayObjectTest extends \Codeception\Test\Unit
{
    /**
     * @covers StudipArrayObject::__construct
     */
    public function testCreation()
    {
        $array = new StudipArrayObject();
        $this->assertCount(0, $array);
    }

    /**
     * @covers StudipArrayObject::__construct
     */
    public function testCreationWithData()
    {
        $array = new StudipArrayObject(['foo' => 'bar', 42 => 23]);
        $this->assertCount(2, $array);
        return $array;
    }

    /**
     * @covers StudipArrayObject::__construct
     */
    public function testCreationWithInvalidIteratorClass()
    {
        $this->expectException(InvalidArgumentException::class);

        $array = new StudipArrayObject([], StudipArrayObject::STD_PROP_LIST, 'DefinitelyNotAnIteratorClass');
    }

    /**
     * @depends testCreationWithData
     * @covers StudipArrayObject::offsetExists
     * @covers StudipArrayObject::offsetGet
     * @covers StudipArrayObject::offsetSet
     * @covers StudipArrayObject::offsetUnset
     */
    public function testArrayAccess(StudipArrayObject $array)
    {
        $this->assertTrue(isset($array['foo']));
        $this->assertFalse(isset($array['bar']));

        $this->assertEquals('bar', $array['foo']);
        $this->assertEquals(23, $array[42]);

        $array['bar'] = 'foo';

        $this->assertCount(3, $array);
        $this->assertTrue(isset($array['bar']));
        $this->assertEquals('foo', $array['bar']);

        unset($array['bar']);
        $this->assertCount(2, $array);
        $this->assertFalse(isset($array['bar']));
    }

    /**
     * @depends testCreationWithData
     * @covers StudipArrayObject::setFlags
     * @covers StudipArrayObject::__isset
     * @covers StudipArrayObject::__get
     * @covers StudipArrayObject::__set
     * @covers StudipArrayObject::__unset
     */
    public function testObjectAccess(StudipArrayObject $array)
    {
        $array->setFlags(StudipArrayObject::ARRAY_AS_PROPS);

        $this->assertTrue(isset($array->foo));
        $this->assertFalse(isset($array->bar));

        $this->assertEquals('bar', $array->foo);

        $array->bar = 'foo';

        $this->assertCount(3, $array);
        $this->assertTrue(isset($array->bar));
        $this->assertEquals('foo', $array->bar);

        unset($array->bar);
        $this->assertCount(2, $array);
        $this->assertFalse(isset($array->bar));

        $array->setFlags(StudipArrayObject::STD_PROP_LIST);

        $this->expectException(InvalidArgumentException::class);
        $test = isset($array->storage);
    }

    public function testAppend()
    {
        $array = new StudipArrayObject();
        $this->assertCount(0, $array);

        $array->append('foo');
        $this->assertCount(1, $array);
    }

    public function testContains()
    {
        $array = new StudipArrayObject([1, 2, 3]);

        $this->assertTrue($array->contains(1));
        $this->assertFalse($array->contains(0));
    }

    public function testSerialization()
    {
        $array = new StudipArrayObject();
        $array->foo = 'bar';

        $serialized = serialize($array);
        $unserialized = unserialize($serialized);

        $this->assertNotFalse($unserialized);

        $this->assertTrue(isset($unserialized->foo));
        $this->assertEquals('bar', $unserialized->foo);

        $this->expectException(InvalidArgumentException::class);
        $test = isset($unserialized->storage);
    }
}
