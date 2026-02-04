<?php

use Lti\ResourceLink;
use Lti\RegistrationPrivacySettings;
use Studip\Lti\Enum\LtiVersion;

final class Lti_ConsentController extends AuthenticatedController
{
    public function edit_action(ResourceLink $resourceLink): void
    {
        $registration = $resourceLink->deployment->registration;
        $privacySettings = RegistrationPrivacySettings::findOneBySQL(
            'registration_id = :registration_id AND user_id = :user_id',
            [
                'registration_id' => $registration->id,
                'user_id' => User::findCurrent()->id
            ]
        );

        if (!$privacySettings) {
            $privacySettings = new RegistrationPrivacySettings();
            $privacySettings->registration_id = $registration->id;
            $privacySettings->user_id = User::findCurrent()->id;
        }

        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();

            if (Request::submitted('save')) {
                if (!Request::get('confirmed')) {
                    PageLayout::postError(_('Ohne die aktive Zustimmung zur Weitergabe Ihrer personenbezogenen Daten können Sie das LTI-Tool nicht nutzen!'));
                    return;
                }

                $privacySettings->accepted = 1;

                //Check which optional fields are allowed to be transmitted to the tool:
                $optionalFieldList = Request::getArray('submit_optional_field');
                $optionalFields = [];
                if (array_key_exists('lang', $optionalFieldList)) {
                    $optionalFields[] = 'lang';
                }
                if (array_key_exists('avatar_url', $optionalFieldList)) {
                    $optionalFields[] = 'avatar_url';
                }

                $privacySettings->allowed_optional_fields = implode(',', $optionalFields);

                $privacySettings->store();

                if (Request::get('redirect') === 'launch') {
                    if ($registration->version == LtiVersion::Lti1p3a->value) {
                        $this->redirect('lti/1p3/index/launch/' . $resourceLink->id);
                    }

                    if ($registration->version == LtiVersion::Lti1P1->value) {
                        $this->redirect('lti/1p1/index/launch/' . $resourceLink->id);
                    }

                    return;
                }
            }
            if (Request::isDialog()) {
                //Close the dialog:
                $this->response->add_header('X-Dialog-Close', '1');
                return;
            } else {
                //Redirect to the LTI tool page of the course:
                $this->redirect('course/lti/index');
            }
        }

        $this->resourceLink = $resourceLink;
        $this->privacySettings = $privacySettings;

        if (Request::get('launch_container') === 'iframe') {
            PageLayout::disableHeader();
            PageLayout::disableFooter();
            PageLayout::disableSidebar();
        }
    }
}
