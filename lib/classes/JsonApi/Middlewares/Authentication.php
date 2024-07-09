<?php

namespace JsonApi\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class Authentication
{
    // der Schlüssel des Request-Attributs, in dem der Stud.IP-Nutzer
    // gefunden werden kann:

    // $user = $request->getAttribute(Authentication::USER_KEY);
    const USER_KEY = 'studip-user';

    /**
     * Der Konstruktor.
     *
     * @param \Closure $authenticator eine Closure, die den Nutzernamen und
     *                                das Passwort als Argumente erhält und
     *                                damit entweder einen Stud.IP-User-Objekt
     *                                oder null zurückgibt
     * @param array    $excluded_strategies
     */
    public function __construct(
        // a callable accepting two arguments username and password and
        // returning either null or a Stud.IP user object
        private readonly \Closure $authenticator,
        private readonly array $excluded_strategies = []
    ) {
    }

    /**
     * Hier muss die Autorisierung implementiert werden.
     *
     * @param Request        $request das Request-Objekt
     * @param RequestHandler $handler der PSR-15 Request Handler
     *
     * @return ResponseInterface das neue Response-Objekt
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $guards = $this->getGuards($request);

        foreach ($guards as $guard) {
            if ($guard->check()) {
                $request = $this->provideUser($request, $guard->user());

                return $handler->handle($request);
            }
        }

        return $this->generateChallenges($guards);
    }

    // according to RFC 2616
    private function generateChallenges(array $guards): ResponseInterface
    {
        $responseFactory = app(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse(401);

        foreach ($guards as $guard) {
            $response = $guard->addChallenge($response);
        }

        return $response;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function provideUser(Request $request, \User $user): Request
    {
        if (
            !isset($GLOBALS['user'])
            || 'nobody' === $GLOBALS['user']->id
        ) {
            $GLOBALS['user'] = new \Seminar_User($user);
            $GLOBALS['auth'] = new \Seminar_Auth();
            $GLOBALS['auth']->auth = [
                'uid' => $user->id,
                'uname' => $user->username,
                'perm' => $user->perms,
            ];
            $GLOBALS['perm'] = new \Seminar_Perm();
            $GLOBALS['MAIL_VALIDATE_BOX'] = false;
            if (isset($GLOBALS['sess'])) {
                $GLOBALS['sess']->delete();
            }
            setTempLanguage($user->id);
        }

        return $request->withAttribute(self::USER_KEY, $user);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getGuards(Request $request): array
    {
        $guards = [
            'session' => new Auth\SessionStrategy(),
            'basic'   => new Auth\HttpBasicAuthStrategy($request, $this->authenticator),
            'oauth2'  => new Auth\OAuth2Strategy($request, $this->authenticator),
        ];

        foreach ($this->excluded_strategies as $strategy) {
            unset($guards[$strategy]);
        }

        return $guards;
    }
}
