<?php
require_once __DIR__ . '/LtiBaseController.php';

use Lti\Publication;
use LTI\LtiBaseController;
use Lti\UserIdentityMapping;
use Studip\Lti\LTI1p3\UserManager;
use Studip\Lti\Enum\UserIdentityMappingContext;

final class Enroll_Lti_ProvisioningModesController extends LtiBaseController
{
    public function index_action(): void
    {
        PageLayout::setTitle(_('Bereitstellungsmodus'));

        $this->callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($this->callbackId);
        if (!isset($callbackData['provisioning_mode'])) {
            throw new AccessDeniedException('Invalid callback data');
        }

        $this->provisioningMode = (int) $callbackData['provisioning_mode'];
    }

    public function create_new_account_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($callbackId);
        if ($callbackData['action'] !== 'enroll_user') {
            throw new AccessDeniedException('Invalid callback action');
        }

        $publication = Publication::find($callbackData['publication_id']);
        $userManager = new UserManager();
        $userManager
            ->setUserIdentity($callbackData['user_identity'])
            ->enroll($publication, $callbackData['local_roles'], $callbackData['registration_id'])
            ->authenticate();

        unset($_SESSION['callbacks'][$callbackId]);

        $this->redirect('course/overview?cid='.$publication->range->id);
    }

    public function reset_account_mapping_action(): void
    {
        $this->allow_nobody = false;
        CSRFProtection::verifyUnsafeRequest();

        $callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($callbackId);
        if ($callbackData['action'] !== 'deeplink_callback') {
            throw new AccessDeniedException('Invalid callback action.');
        }

        UserIdentityMapping::deleteBySQL(
            "user_id = :user_id AND context = :context",
            [
                'user_id' => User::findCurrent()->id,
                'context' => UserIdentityMappingContext::DeepLink->value
            ]
        );

        sess()->destroy();
        sess()->start();
        $_SESSION['callbacks'][$callbackId] = $callbackData;
        $_SESSION['redirect_after_login'] = URLHelper::getLink('dispatch.php/enroll/lti/contents?callback_id=' . $callbackId);

        $this->redirect('login?callback_id=' . $callbackId);
    }
}
