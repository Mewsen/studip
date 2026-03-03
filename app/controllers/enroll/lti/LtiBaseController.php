<?php
namespace LTI;

use User;
use PageLayout;
use LtiToolModule;
use AccessDeniedException;
use AuthenticatedController;
use Studip\OAuth2\NegotiatesWithPsr7;

abstract class LtiBaseController extends AuthenticatedController
{
    use NegotiatesWithPsr7;
    protected $allow_nobody = true;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!LtiToolModule::isToolSharingEnabled()) {
            throw new AccessDeniedException();
        }

        PageLayout::disableSidebar();
        PageLayout::setBodyElementId('lti');
    }

    protected function storeUserLocale(User $user, ?string $locale): self
    {
        if ($locale && str_starts_with($locale, 'en')) {
            $_SESSION['_language'] = 'en_GB';
            $user->preferred_language = 'en_GB';
            $user->store();
        }

        return $this;
    }

    protected function validateCallbackData(string $callbackId): array
    {
        if (empty($_SESSION['callbacks'][$callbackId])) {
            throw new AccessDeniedException('Missing or invalid callback ID');
        }

        $callbackData = $_SESSION['callbacks'][$callbackId];
        if (
            $callbackData['context'] !== 'lti'
            || $callbackData['expires_at'] < time()
        ) {
            throw new AccessDeniedException('Invalid or expired callback data');
        }

        return $callbackData;
    }
}
