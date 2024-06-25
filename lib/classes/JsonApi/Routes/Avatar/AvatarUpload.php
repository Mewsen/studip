<?php

namespace JsonApi\Routes\Avatar;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\ValidationTrait;
use JsonApi\NonJsonApiController;
use JsonApi\Routes\Files\RoutesHelperTrait as FilesRoutesHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\UploadedFile;

/**
 * Create an Avatar.
 */
class AvatarUpload extends NonJsonApiController
{
    use ValidationTrait;
    use AvatarHelpers;
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = $this->getUser($request);
        $json = $this->validate($request);
        $range_id = self::arrayGet($json, 'data.range-id');
        $range_type = self::arrayGet($json, 'data.range-type');

        ['class' => $class, 'has_perm' => $has_perm] = self::getAvatarClass($range_id, $range_type, $user);

        if (!$has_perm) {
            throw new AuthorizationFailedException();
        }

        $avatar = $class::getAvatar($range_id);
        $imgdata_string = self::arrayGet($json, 'data.image');
        [$type, $imgdata_part] = explode(';', $imgdata_string);
        [$base, $imgdata_base64] = explode(',', $imgdata_part);
        $imgdata = base64_decode($imgdata_base64);
        // Write data to file.
        $filename = $GLOBALS['TMP_PATH'] . '/avatar-' . $range_id . '.webp';
        file_put_contents($filename, $imgdata);

        // Use new image file for avatar creation.
        $avatar->createFrom($filename);
        
        
        return $response->withStatus(201);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.range-id')) {
            return 'New avatar must have an `range-id`.';
        }
        if (!self::arrayHas($json, 'data.range-type')) {
            return 'New avatar must have a `range-type`.';
        }
        if (!self::arrayHas($json, 'data.image')) {
            return 'New avatar must have a `image`.';
        }
    }
}