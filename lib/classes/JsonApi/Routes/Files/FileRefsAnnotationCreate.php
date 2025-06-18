<?php

namespace JsonApi\Routes\Files;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Errors\UnprocessableEntityException;
use JsonApi\NonJsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FileRefsAnnotationCreate extends NonJsonApiController
{
    use RoutesHelperTrait;

    public function invoke(Request $request, Response $response, array $args): Response
    {
        $user = $this->getUser($request);

        $originalFileRef = \FileRef::find($args['id']);
        if (!$originalFileRef) {
            throw new RecordNotFoundException();
        }

        if ('application/pdf' !== $originalFileRef->file->mime_type) {
            throw new UnprocessableEntityException();
        }

        if (!Authority::canAnnotateFileRef($user, $originalFileRef)) {
            throw new AuthorizationFailedException();
        }

        $folder = $originalFileRef->folder->getTypedFolder();
        $fileRef = $this->handleUpload($request, $folder);
        $fileRef->content_terms_of_use_id = $originalFileRef->content_terms_of_use_id;
        $fileRef->folder_id = $originalFileRef->folder_id;

        // Store annotated file, updating its metadata.
        $fileRef->setAnnotatedFileVersion($originalFileRef, $user);
        $fileRef->store();

        return $this->redirectToFileRef($response, $fileRef);
    }
}
