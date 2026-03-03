<?php
require_once __DIR__ . '/LtiBaseController.php';

use Trails\Dispatcher;
use LTI\LtiBaseController;
use Studip\Lti\LTI1p3\ToolManager;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;

final class Enroll_Lti_JwksController extends LtiBaseController
{
    protected $with_session = false;

    public function __construct(
        protected Dispatcher $dispatcher,
        protected JwksRequestHandler $jwksRequestHandler
    )
    {
        parent::__construct($dispatcher);
    }

    public function index_action(): void
    {
        $toolKeyring = ToolManager::getKeyChain();

        $this->renderPsrResponse(
            $this->jwksRequestHandler->handle($toolKeyring->getKeySetName())
        );
    }
}
