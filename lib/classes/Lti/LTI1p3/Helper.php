<?php
namespace Studip\Lti\LTI1p3;

class Helper
{
    public static function parseLineItemIdentifier(string $lineItemIdentifier): array
    {
        $query = parse_url($lineItemIdentifier, PHP_URL_QUERY);
        parse_str($query, $query);

        return [
            'resource_link_id' => $query['resource_link_id'] ?? null,
            'line_item_id' => $query['line_item_id'] ?? null
        ];
    }

}
