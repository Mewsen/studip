<?php
/*
 * TestBlock.php - Courseware Vips test block
 * Copyright (c) 2022  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

namespace Courseware\BlockTypes;

use VipsAssignment;
use VipsModule;

class TestBlock extends BlockType
{
    /**
     * Get a short string describing this type of block.
     */
    public static function getType(): string
    {
        return 'test';
    }

    /**
     * Get the title of this type of block.
     */
    public static function getTitle(): string
    {
        return _('Aufgabenblatt');
    }

    /**
     * Get the description of this type of block.
     */
    public static function getDescription(): string
    {
        return _('Stellt ein vorhandenes Aufgabenblatt bereit.');
    }

    /**
     * Get the initial payload of every instance of this block.
     */
    public function initialPayload(): array
    {
        return ['assignment' => ''];
    }

    /**
     * Get the JSON schema for the payload of this block type.
     */
    public static function getJsonSchema(): string
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'assignment' => [
                    'type' => 'string'
                ]
            ]
        ];

        return json_encode($schema);
    }

    /**
     * Get the list of categories for this block type.
     */
    public static function getCategories(): array
    {
        return ['interaction'];
    }

    /**
     * Get the list of content types for this block type.
     */
    public static function getContentTypes(): array
    {
        return ['rich'];
    }

    /**
     * Get the list of file types for this block type.
     */
    public static function getFileTypes(): array
    {
        return [];
    }

    /**
     * Copy the payload of this block into the given range id.
     */
    public function copyPayload(string $rangeId = ''): array
    {
        static $assignments = [];

        $context = $rangeId === $GLOBALS['user']->id ? 'user' : 'course';
        $payload = $this->getPayload();

        if ($payload['assignment']) {
            $assignment = VipsAssignment::find($payload['assignment']);
        }

        if (!$assignment || !$assignment->checkEditPermission()) {
            return $this->initialPayload();
        }

        if ($context === 'course' && !VipsModule::hasStatus('tutor', $rangeId)) {
            return $this->initialPayload();
        }

        if ($assignment->range_id !== $rangeId) {
            if (!isset($assignments[$assignment->id])) {
                $copy = $assignment->copyIntoCourse($rangeId, $context);
                $assignments[$assignment->id] = $copy->id;
            }

            $payload['assignment'] = $assignments[$assignment->id];
        }

        return $payload;
    }
}
