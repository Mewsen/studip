<?php

namespace Studip\Forms;

use CaptchaChallenge;

/**
 * The Text class represents a part of a form that displays a captcha.
 */
class Captcha extends Fieldset
{
    private CaptchaInput $captcha_input;

    public function __construct()
    {
        parent::__construct(_('Bitte bestätigen Sie, dass Sie kein Roboter sind'));

        $captchaInput = new CaptchaInput('altcha', $this->legend, null);
        $captchaInput->setStoringFunction(function (string $payload) {
            $json = CaptchaChallenge::decodePayload($payload);

            CaptchaChallenge::create([
                'salt'   => $json['salt'],
                'number' => $json['number'],
            ]);
        });
        $this->addInput($captchaInput);
    }
}
