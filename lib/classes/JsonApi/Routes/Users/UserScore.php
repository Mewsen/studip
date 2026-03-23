<?php
namespace JsonApi\Routes\Users;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\NonJsonApiController;

class UserScore extends NonJsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$observedUser = \User::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowScore($this->getUser($request), $observedUser)) {
            throw new AuthorizationFailedException();
        }

        $score = \Score::GetMyScore($observedUser);
        $title = \Score::getTitel($score, $observedUser->geschlecht);
        $kingIcons = $observedUser->getStudipKingIcon(true);

        $response->getBody()->write(json_encode([
            'score' => number_format((int) $score, 0, ',', '.'),
            'title' => $title,
            'kings' => $kingIcons,
        ]));

        return $response->withHeader('Content-type', 'application/json');
    }
}
