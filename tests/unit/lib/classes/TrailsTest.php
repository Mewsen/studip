<?php
class TrailsTest extends \Codeception\Test\Unit
{
    /**
     * @covers Trails\Inflector::camelize
     */
    public function testInflectorCamelize(): void
    {
        $this->assertEquals(
            'Path_SubPath_UnderscoreController',
            Trails\Inflector::camelize('path/sub_path/underscore_controller')
        );
    }

    /**
     * @covers Trails\Inflector::underscore
     */
    public function testInflectorUnderscore(): void
    {
        $this->assertEquals(
            'path/sub_path/underscore_controller',
            Trails\Inflector::underscore('Path_SubPath_UnderscoreController')
        );
    }
}
