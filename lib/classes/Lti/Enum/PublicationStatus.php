<?php
namespace Studip\Lti\Enum;

enum PublicationStatus: string {
    case Active = 'active';
    case Inactive = 'inactive';

    use StatusHelpers;
}
