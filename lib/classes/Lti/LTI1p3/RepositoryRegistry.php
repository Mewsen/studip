<?php
namespace Studip\Lti\LTI1p3;

use DI;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\User\UserAuthenticatorInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidator;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidator;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidatorInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidatorInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidatorInterface;

final class RepositoryRegistry
{
    public static function definitions(): array
    {
        return [
            RegistrationRepositoryInterface::class => DI\get(RegistrationManager::class),
            NonceRepositoryInterface::class => DI\get(NonceRepository::class),
            PlatformLaunchValidatorInterface::class => DI\get(PlatformLaunchValidator::class),
            ToolLaunchValidatorInterface::class => DI\get(ToolLaunchValidator::class),
            UserAuthenticatorInterface::class => DI\get(UserAuthenticator::class),
            LineItemRepositoryInterface::class => DI\get(LineItemRepository::class),
            RequestAccessTokenValidatorInterface::class => DI\get(RequestAccessTokenValidator::class),
            KeyChainRepositoryInterface::class => DI\factory(function() {
                return new KeyChainRepository([
                    PlatformManager::getKeyChain(),
                    ToolManager::getKeyChain()
                ]);
            }),
        ];
    }
}
