<?php

namespace JsonApi\Routes\StockImages;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\NonJsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Nyholm\Psr7\UploadedFile;
use Studip\StockImages\Scaler;
use Studip\StockImages\PaletteCreator;

class StockImagesZipUpload extends NonJsonApiController
{
    use UploadHelpers;

    public function __invoke(Request $request, Response $response, $args): Response
    {
        if (!Authority::canUploadStockImage($this->getUser($request))) {
            throw new AuthorizationFailedException();
        }
        $image_count = $this->handleUpload($request);

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['image-count' => $image_count]));

        return $response;
    }

    private function handleUpload(Request $request): int
    {
        $uploadedFile = self::getUploadedFile($request);
        if (UPLOAD_ERR_OK !== $uploadedFile->getError()) {
            $error = self::getErrorString($uploadedFile->getError());
            throw new BadRequestException($error);
        }

        $validateError = self::validate($uploadedFile);
        if (!empty($validateError)) {
            throw new BadRequestException($validateError);
        }

        $tmp_path = $GLOBALS['TMP_PATH'] . '/stock-images/';

        if (!file_exists($tmp_path)) {
            mkdir($tmp_path);
        }
        $zip_path = $tmp_path . 'archiv.zip';
        $uploadedFile->moveTo($zip_path);
        $zip = new \ZipArchive;
        if ($zip->open($zip_path) === TRUE) {
            $zip->extractTo($tmp_path);
            $zip->close();
        } else {
            $this->cleanTmp($tmp_path);
            throw new BadRequestException('Can not extract Zip file.');
        }
        $csv_file = file($tmp_path . 'meta.csv');
        if (!$csv_file) {
            $this->cleanTmp($tmp_path);
            throw new BadRequestException('No meta.csv file provided.');
        }
        $rows = array_map(
            fn($v) => str_getcsv($v, ';'),
            $csv_file
        );
        $header = array_shift($rows);
        $images = [];
        foreach ($rows as $row) {
            $images[] = array_combine($header, $row);
        }

        $image_counter = 0;
        foreach ($images as $i => $meta) {
            $filename = $meta['filename'];
            if (!$filename) {
                continue;
            }
            $filepath = $tmp_path . $filename;
            $filesize = filesize($filepath);
            $imagesize = getimagesize($filepath);

            $image = \StockImage::create([
                'title' => $meta['title'] ?? 'unknown',
                'description' => $meta['description'] ?? '',
                'license' => $meta['license'] ?? '',
                'author' => $meta['author'] ?? '',
                'height' => $imagesize[1],
                'width' => $imagesize[0],
                'mime_type' => $imagesize['mime'],
                'size' => $filesize,
                'tags' => json_encode(explode(',', $meta['tags'])),
            ]);

            copy($filepath, $image->getPath());
            $scaler = new \Studip\StockImages\Scaler();
            $scaler($image);
            $paletteCreator = new \Studip\StockImages\PaletteCreator();
            $paletteCreator($image);

            $image_counter++;
        }

        $this->cleanTmp($tmp_path);

        return $image_counter;
    }

    private function cleanTmp(string $tmp_path): void
    {
        array_map('unlink', glob("$tmp_path/*.*"));
        rmdir($tmp_path);
    }

    /**
     * @return string|null null, if the file is valid, otherwise a string containing the error
     */
    private function validate(UploadedFile $file)
    {
        $mimeType = $file->getClientMediaType();
        if (!in_array($mimeType, ['application/x-zip-compressed', ' application/x-zip', 'application/zip'])) {
            return 'Unsupported archive type.';
        }
    }
}