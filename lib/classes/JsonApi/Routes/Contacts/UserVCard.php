<?php

namespace JsonApi\Routes\Contacts;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\InternalServerError;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\NonJsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserVCard extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if (!$observedUser = \User::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!Authority::canDownloadUserVCard($user, $observedUser)) {
            throw new AuthorizationFailedException();
        }

        $vcard = \vCard::export($observedUser);
        $response->getBody()->write($vcard);

        $filename = _('Kontakte');

        return $response
            ->withHeader('Content-Type', 'text/x-vCard;charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; ' . encode_header_parameter('filename', $filename . '.vcf'))
            ->withHeader('Pragma', 'private');
    }
}
