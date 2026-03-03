<?php

use Trails\Dispatcher;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\Lti\LTI1p3\PlatformManager;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;

class Lti_1p3_JwksController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;
    use NegotiatesWithPsr7;

    public function __construct(
        protected Dispatcher $dispatcher,
        protected JwksRequestHandler $jwksRequestHandler
    )
    {
        parent::__construct($dispatcher);
    }

    public function index_action(): void
    {
        $platformKeyring = PlatformManager::getKeyring();

        $this->renderPsrResponse(
            $this->jwksRequestHandler->handle($platformKeyring->range_id)
        );
    }
}
