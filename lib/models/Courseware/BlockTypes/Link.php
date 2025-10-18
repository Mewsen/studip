<?php

namespace Courseware\BlockTypes;

/**
 * This class represents the content of a Courseware link block.
 *
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 5.0
 */
class Link extends BlockType
{
    public static function getType(): string
    {
        return 'link';
    }

    public static function getTitle(): string
    {
        return _('Link');
    }

    public static function getDescription(): string
    {
        return _('Erstellt einen Link innerhalb der Courseware oder auf eine andere Seite.');
    }

    public function initialPayload(): array
    {
        return [
            'type' => 'external',
            'target' => '',
            'unit-target' => '',
            'url' => '',
            'title' => '',
        ];
    }

    public static function getJsonSchema(): string
    {
        $schemaFile = __DIR__ . '/Link.json';
        return file_get_contents($schemaFile);
    }

    public function performMapping(array $mapping, \Courseware\Unit $newUnit): void
    {
        ['elements' => $elements] = $mapping;
        $payload = $this->getPayload();
        if ($payload['type'] === 'internal' && '' != $payload['target']) {
            if (in_array($payload['target'], array_keys($elements))) {
                $payload['target'] = $elements[intval($payload['target'])];
            } else {
                $payload['target'] = '';
            }
            $this->setPayload($payload);
            $this->block->store();
        }
    }

    public static function getCategories(): array
    {
        return ['layout', 'external'];
    }

    public static function getContentTypes(): array
    {
        return ['text', 'layout', 'link'];
    }

    public static function getFileTypes(): array
    {
        return [];
    }

    public static function getTags(): array
    {
        return [
            _('URL'),
            _('Verlinkung'),
            _('Webseite'),
            _('extern'),
            _('Weiterleiten'),
            _('Material'),
            _('Zusatz'),
            _('Weiterleitung'),
            _('intern'),
            _('Verweis'),
            _('Index'),
            _('Hyperlink'),
            _('Quellenangabe'),
            _('Linkliste'),
            _('Linksammlung')
        ];
    }
}
