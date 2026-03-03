<?php
require_once __DIR__ . '/LtiBaseController.php';

use Trails\Dispatcher;
use LTI\LtiBaseController;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcInitiationRequestHandler;

final class Enroll_Lti_AuthInitController extends LtiBaseController
{
    protected $with_session = false;

    public function __construct(
        protected Dispatcher $dispatcher,
        protected OidcInitiationRequestHandler $oidcInitHandler
    )
    {
        parent::__construct($dispatcher);
    }

    public function index_action(): void
    {
        $this->renderPsrResponse(
            $this->oidcInitHandler->handle($this->getPsrRequest())
        );
    }
}
