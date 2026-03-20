<?php
namespace Studip\Lti\Enum;

use Studip\Lti\Trait\StatusHelpers;

enum PublicationStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    use StatusHelpers;
}
