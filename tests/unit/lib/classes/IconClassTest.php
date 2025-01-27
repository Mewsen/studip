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

    public function testIconCreateAsImg()
    {
        $this->assertEquals(
            '<img src="images/icons/blue/vote.svg" alt="" class="studip-icon icon-role-clickable icon-shape-vote">',
            Icon::create('vote')->asImg()
        );
    }

    public function testIconCreateAsImgWithAddition()
    {
        $this->assertEquals(
            '<img src="images/icons/blue/vote.svg" alt="" class="studip-icon icon-role-clickable icon-shape-vote">',
            Icon::create('vote')->asImg()
        );
    }

    public function testIconCreateAsImgWithSize()
    {
        $this->assertEquals(
            '<img style="width:100px;height:100px" src="images/icons/blue/vote.svg" alt="" class="studip-icon icon-role-clickable icon-shape-vote">',
            Icon::create('vote')->asImg(100)
        );
    }

    public function testIconCreateAsImgWithTitle()
    {
        $this->assertEquals(
            '<img title="Mit Anhang" style="width:24px;height:24px" src="images/icons/blue/vote.svg" class="studip-icon icon-role-clickable icon-shape-vote">',
            Icon::create('vote')->asImg(24, ['title' => 'Mit Anhang'])
        );
    }

    public function testIconCreateAsImgWithHspace()
    {
        $this->assertEquals(
            '<img hspace="3" src="images/icons/blue/arr_2left.svg" alt="" class="studip-icon icon-role-clickable icon-shape-arr_2left">',
            Icon::create('arr_2left')->asImg(['hspace' => 3])
        );
    }

    public function testIconCreateAsImgWithClass()
    {
        $this->assertEquals(
            '<img class="text-bottom studip-icon icon-role-info icon-shape-staple" style="width:24px;height:24px" src="images/icons/black/staple.svg" alt="">',
            Icon::create('staple', Icon::ROLE_INFO)->asImg(24, ['class' => 'text-bottom'])
        );
    }

    public function testIconCreateAsImgWithClassAndTitle()
    {
        $this->assertEquals(
            '<img class="text-bottom studip-icon icon-role-new icon-shape-upload" title="Datei hochladen" style="width:24px;height:24px" src="images/icons/red/upload.svg">',
            Icon::create('upload', Icon::ROLE_NEW, ['title' => 'Datei hochladen'])
                ->asImg(24, ['class' => 'text-bottom'])
        );
    }

    public function testIconCreateAsInput()
    {
        $this->assertEquals(
            '<input type="image" class="text-bottom studip-icon icon-role-clickable icon-shape-upload" style="width:24px;height:24px" src="images/icons/blue/upload.svg" alt="">',
            Icon::create('upload')->asInput(24, ['class' => 'text-bottom'])
        );
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

    public function testIconCreateAsCSSWithSize()
    {
        $this->assertEquals(
            'background-image:url(images/icons/blue/vote.svg);background-size:17px 17px;',
            Icon::create('vote')->asCSS(17)
        );
    }

    public function testIconCreateAsImagePath()
    {
        $this->assertEquals(
            'images/icons/blue/vote.svg',
            Icon::create('vote')->asImagePath()
        );
    }

    public function testIconCreateAsImgWithoutSize()
    {
        $this->assertEquals(
            '<img src="images/icons/blue/vote.svg" alt="" class="studip-icon icon-role-clickable icon-shape-vote">',
            Icon::create('vote')->asImg(false)
        );
    }

    public function testIconCreateAsInputWithoutSize()
    {
        $this->assertEquals(
            '<input type="image" src="images/icons/blue/upload.svg" alt="" class="studip-icon icon-role-clickable icon-shape-upload">',
            Icon::create('upload')->asInput(false)
        );
    }

    public function testIconCreateRemovedExtras()
    {
        $this->assertEquals(
            '<img src="images/icons/blue/vote.svg" alt="" class="studip-icon icon-role-clickable icon-shape-vote">',
            Icon::create('add/vote')->asImg(false)
        );
        $this->assertEquals(
            '<img src="images/icons/blue/vote.svg" alt="" class="studip-icon icon-role-clickable icon-shape-vote">',
            Icon::create('vote+add')->asImg(false)
        );
    }
}
