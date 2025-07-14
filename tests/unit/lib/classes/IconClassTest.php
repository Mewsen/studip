<?php
/*
 * Copyright (C) 2015 <mlunzena@uos.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class IconClassTest extends \Codeception\Test\Unit
{
    private $memo_assets_url;

    public function setUp(): void
    {
        $this->memo_assets_url = Assets::url();
        Assets::set_assets_url('');
    }

    public function tearDown(): void
    {
        Assets::set_assets_url($this->memo_assets_url);
    }

     public function testIconIsImmutable()
     {
         $icon = Icon::create('upload', attributes: ['title' => 'a title']);
         $copy = $icon->copyWithRole(Icon::ROLE_CLICKABLE);

         $this->assertNotSame($icon, $copy);
     }

     public function testIconCopyWithRole()
     {
         $icon = Icon::create('upload', attributes: ['title' => 'a title']);
         $copy = $icon->copyWithRole(Icon::ROLE_INFO);

         $this->assertEquals($icon->getShape(),      $copy->getShape());
         $this->assertNotEquals($icon->getRole(),    $copy->getRole());
         $this->assertEquals($icon->getAttributes(), $copy->getAttributes());
     }

     public function testIconCopyWithShape()
     {
         $icon = Icon::create('upload', attributes: ['title' => 'a title']);
         $copy = $icon->copyWithShape('staple');

         $this->assertNotEquals($icon->getShape(),   $copy->getShape());
         $this->assertEquals($icon->getRole(),       $copy->getRole());
         $this->assertEquals($icon->getAttributes(), $copy->getAttributes());
     }

     public function testIconCopyWithAttributes()
     {
         $icon = Icon::create('upload', Icon::ROLE_CLICKABLE, ['title' => 'a title']);
         $copy = $icon->copyWithAttributes(['title' => 'another title']);

         $this->assertEquals($icon->getShape(),         $copy->getShape());
         $this->assertEquals($icon->getRole(),          $copy->getRole());
         $this->assertNotEquals($icon->getAttributes(), $copy->getAttributes());
     }

     public function testStaticIcon()
     {
         $icon = Icon::create('https://i.imgur.com/kpTtTh.gif');
         $this->assertEquals($icon->asImagePath(), 'https://i.imgur.com/kpTtTh.gif');
     }
}
