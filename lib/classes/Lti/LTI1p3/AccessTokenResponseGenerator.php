<?php
namespace Studip\Lti\LTI1p3;

use Studip\Cache\Factory;
use Studip\OAuth2\Bridge\ScopeEntity;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ScopeRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ClientRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\AccessTokenRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Factory\AuthorizationServerFactory;
use OAT\Library\Lti1p3Core\Security\OAuth2\Generator\AccessTokenResponseGenerator as BaseAccessTokenResponseGenerator;

final class AccessTokenResponseGenerator extends BaseAccessTokenResponseGenerator
{
    const SCOPES = [
        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
    ];
    public function __construct() {
        parent::__construct(
            new KeyChainRepository(),
            new AuthorizationServerFactory(
                new ClientRepository(new RegistrationManager()),
                new AccessTokenRepository(Factory::getCache()),
                $this->getScopeRepository(),
                PlatformManager::getPrivateKey()->getContent()
            )
        );
    }

    private function getScopeRepository(): ScopeRepository
    {
        $scopeEntities = array_map(
            static fn(string $scope) => new ScopeEntity($scope),
            self::SCOPES
        );

        return new ScopeRepository($scopeEntities);

    }
}
