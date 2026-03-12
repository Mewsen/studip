<?php
namespace Studip\Lti\LTI1p3;

use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository as BaseKeyChainRepository;

final class KeyChainRepository extends BaseKeyChainRepository
{
    public function __construct() {
        parent::__construct([
            PlatformManager::getKeyChain(),
            ToolManager::getKeyChain()
        ]);
    }
}
