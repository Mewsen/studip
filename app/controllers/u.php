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


    public function create_action()
    {
        if (!Request::isPost()) {
            throw new AccessDeniedException();
        }

        $user = User::findCurrent();
        $path = Request::get('path');
        //Check if the user has already created such a short-URL:
        $short_url = ShortURL::findOneBySql(
            'url = :path AND user_id = :user_id',
            [
                'path' => $path,
                'user_id' => $user->id
            ]
        );
        if (!$short_url) {
            $short_url = new ShortURL();
            $short_url->url = $path;
            $short_url->user_id = $user->id;
            $short_url->store();
        }
        $this->render_json(
            [
                'full_short_url' => URLHelper::getURL('dispatch.php/u/r/' . $short_url->alias),
                'url_id' => $short_url->id
            ]
        );
    }


    public function alias_action($url_id)
    {
        $short_url = new ShortURL($url_id);
        $this->form = \Studip\Forms\Form::fromSORM(
            $short_url,
            [
                'fields' => [
                    'alias' => [
                        'label' => _('Bezeichnung'),
                        'type' => 'text',
                        'pattern' => '[a-záæäéèôøöü0-9\-]{4,256}'
                    ]
                ]
            ]
        );
        $this->form->autoStore();
    }
}
