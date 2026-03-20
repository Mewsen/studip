<?php
namespace Studip\Lti\Enum;

use Studip\Lti\Trait\StatusHelpers;

enum RegistrationStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    use StatusHelpers;
}
