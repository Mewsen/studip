<?php


/**
 * The U controller is responsible for short URLs. Therefore, it is just named "u" to not waste space.
 */
class UController extends AuthenticatedController
{
    /**
     * The resolve action for a short URL. Shortened, to be short itself.
     */
    public function r_action($id)
    {
        $url = ShortURL::findOneBySql('id = :id OR alias = :id', ['id' => $id]);
        if ($url) {
            $this->redirect($url->url);
        } else {
            throw new AccessDeniedException(_('Die Kurz-URL ist ungültig!'));
        }
    }


    public function add_action()
    {
        $short_url = new ShortURL();
        $short_url->url = Request::get('from_path');
        $short_url->user_id = $GLOBALS['user']->id;
        $this->form = \Studip\Forms\Form::fromSORM(
            $short_url,
            [
                'fields' => [
                    'alias' => [
                        'label' => _('URL-Bezeichnung'),
                        'type' => 'text',
                        'pattern' => '[a-záæäéèôøöü0-9\-]{4,256}'
                    ],
                    'url' => [
                        'type' => 'hidden'
                    ]
                ]
            ]
        );
        $this->form->autoStore();
    }
}
