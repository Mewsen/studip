<?php
namespace Studip\Lti\Trait;

use User;
use PageLayout;
use Lti\Registration;
use Lti\RegistrationPrivacySettings;
use Studip\Lti\Enum\RegistrationStatus;

trait RegistrationValidationTrait {
    public function validateRegistrationStatus(Registration $registration): bool
    {
        if ($registration->status === RegistrationStatus::Inactive->value) {
            PageLayout::postError(sprintf(
                _('Die LTI-Registrierung „%s“ ist deaktiviert.'),
                htmlReady($registration->name)
            ));
            $this->redirect('course/lti');
            return false;
        }

        return true;
    }

    public function validateUserConsent(Registration $registration): bool
    {
        $registrationConfigs = $registration->getConfigValues();
        $dataProtectionConsent = RegistrationPrivacySettings::countBySQL(
            "`registration_id` = :registration_id AND `user_id` = :user_id AND `accepted` = 1",
            [
                'registration_id' => $registration->id,
                'user_id' => User::findCurrent()->id
            ]
        );


        $launchContainer = $resourceLink->launch_container ?? $registrationConfigs['launch_container'];

        if (!$dataProtectionConsent) {
            $this->redirect('lti/consent/edit/' . $resourceLink->id, [
                'redirect' => 'launch',
                'launch_container' => $launchContainer
            ]);
            return false;
        }

        return true;
    }
}
