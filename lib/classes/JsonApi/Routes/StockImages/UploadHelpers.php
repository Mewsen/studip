<?php

namespace JsonApi\Routes\StockImages;

use JsonApi\Errors\BadRequestException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Nyholm\Psr7\UploadedFile;

trait UploadHelpers
{
    protected static function getUploadedFile(Request $request): UploadedFile
    {
        $files = iterator_to_array(self::getUploadedFiles($request));

        if (0 === count($files)) {
            throw new BadRequestException('File upload required.');
        }

        if (count($files) > 1) {
            throw new BadRequestException('Multiple file upload not possible.');
        }

        $uploadedFile = reset($files);
        if (UPLOAD_ERR_OK !== $uploadedFile->getError()) {
            throw new BadRequestException('Upload error.');
        }

        return $uploadedFile;
    }

    /**
     * @return iterable<UploadedFile> a list of uploaded files
     */
    protected static function getUploadedFiles(Request $request): iterable
    {
        foreach ($request->getUploadedFiles() as $item) {
            if (!is_array($item)) {
                yield $item;
                continue;
            }
            foreach ($item as $file) {
                yield $file;
            }
        }
    }

    protected static function getErrorString(int $errNo): string
    {
        $errors = [
            UPLOAD_ERR_OK => 'There is no error, the file uploaded with success',
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        ];

        return $errors[$errNo] ?? '';
    }
}