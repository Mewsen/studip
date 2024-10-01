<?php

namespace JsonApi\Routes\StockImages;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\NonJsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Studip\StockImages\Scaler;
use Studip\StockImages\PaletteCreator;

class StockImagesUpload extends NonJsonApiController
{
    use UploadHelpers;
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $resource = \StockImage::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canUploadStockImage($this->getUser($request))) {
            throw new AuthorizationFailedException();
        }

        $this->handleUpload($request, $resource);
        $this->processStockImage($resource);

        return $this->redirectToStockImage($response, $resource);
    }

    private function handleUpload(Request $request, \StockImage $resource): void
    {
        $uploadedFile = self::getUploadedFile($request);
        if (UPLOAD_ERR_OK !== $uploadedFile->getError()) {
            $error = self::getErrorString($uploadedFile->getError());
            throw new BadRequestException($error);
        }

        $error = self::validate($uploadedFile);
        if (!empty($error)) {
            throw new BadRequestException($error);
        }

        $resource->mime_type = $uploadedFile->getClientMediaType();
        $resource->size = $uploadedFile->getSize();
        $uploadedFile->moveTo($resource->getPath());

        $imageSize = getimagesize($resource->getPath());
        $resource->width = $imageSize[0];
        $resource->height = $imageSize[1];

        $resource->store();
    }

    /**
     * @return string|null null, if the file is valid, otherwise a string containing the error
     */
    private function validate(UploadedFileInterface $file)
    {
        $mimeType = $file->getClientMediaType();
        if (!in_array($mimeType, ['image/gif', 'image/jpeg', 'image/png', 'image/webp'])) {
            return 'Unsupported media type.';
        }
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function redirectToStockImage(Response $response, \StockImage $stockImage): Response
    {
        $pathinfo = $this->getSchema($stockImage)
            ->getSelfLink($stockImage)
            ->getStringRepresentation($this->container->get('json-api-integration-urlPrefix'));
        $old = \URLHelper::setBaseURL($GLOBALS['ABSOLUTE_URI_STUDIP']);
        $url = \URLHelper::getURL($pathinfo, [], true);
        \URLHelper::setBaseURL($old);

        return $response->withHeader('Location', $url)->withStatus(201);
    }

    private function processStockImage(\StockImage $resource): void
    {
        $scaler = new Scaler();
        $scaler($resource);
        $paletteCreator = new PaletteCreator();
        $paletteCreator($resource);
    }
}
