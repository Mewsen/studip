<?php
final class CaptchaController extends StudipController
{
    public function challenge_action(): void
    {
        $this->response->add_header(
            'Expires',
            gmdate('D, d M Y H:i:s', time() + CaptchaChallenge::CHALLENGE_EXPIRATION) . ' GMT'
        );
        $this->render_json(CaptchaChallenge::createNewChallenge());
    }
}
