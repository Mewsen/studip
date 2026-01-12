<?php
namespace Studip\Lti\Enum;

enum RegistrationStatus: string {
    case Active = 'active';
    case Inactive = 'inactive';

    use StatusHelpers;
}
