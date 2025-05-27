<?php

class UController extends AuthenticatedController
{
    public function r_action(string $short_url_alias): void
    {
        $short_url = ShortUrl::findOneBySQL('alias = ?', [$short_url_alias]);

        if (!$short_url) {
            PageLayout::postError(_('Diese Kurz-URL existiert nicht.'));
            $this->redirect($this->url_for('start'));
            return;
        }
        $this->redirect(URLHelper::getURL($short_url->path));
    }
}
