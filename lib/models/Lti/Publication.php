<?php
namespace Lti;

use Range;
use SimpleORMap;

class Publication extends SimpleORMap
{
    protected static function configure($config = []): void
    {
        $config['db_table'] = 'lti_publications';

        $config['additional_fields']['range'] = [
            'set' => function (Registration $registration, string $field, Range | string | null $range) {
                if ($range instanceof Range) {
                    $registration->range_id = $range->getRangeId();
                }
            },
            'get' => function (Registration $registration): ?Range {
                return get_object_by_range_id($registration->range_id);
            },
        ];

        parent::configure($config);
    }

    public function transformData(): array
    {
        return [
            ...$this->toRawArray(),
            'state' => (bool) $this->state,
            'range_name' => $this->range?->getFullName(),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];
    }
}
