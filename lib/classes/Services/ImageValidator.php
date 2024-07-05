<?php
namespace Studip\Services;

final class ImageValidator
{
    public const VALID_EXTENSIONS = [
        'gif',
        'jpeg', 'jpg',
        'png',
        'webp',
    ];

    public const VALID_MIMETYPES = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function validate(string $filename): bool
    {
        return $this->validateName($filename)
            && $this->validateMimeType(get_mime_type($filename))
            && $this->validateContents($filename);
    }

    public function validateMimeType(string $mime_type): bool
    {
        return str_starts_with($mime_type, 'image/')
            && in_array($mime_type, self::VALID_MIMETYPES);
    }

    public function validateName(string $filename): bool
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        return in_array($extension, self::VALID_EXTENSIONS);
    }

    public function validateContents(string $filename): bool
    {
        $check = imagecreatefromstring(file_get_contents($filename));
        if ($check === false) {
            return false;
        }

        imagedestroy($check);

        return true;
    }
}
