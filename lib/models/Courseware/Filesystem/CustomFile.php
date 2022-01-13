<?php

namespace Courseware\Filesystem;

/**
 * This class represents the metdata for a custom file in a courseware block
 */
class CustomFile
{
    /**
     * The payload for this custom file, containt id, block_id and arbitrary attributes
     * @var [type]
     */
    protected
        $payload;

    /**
     * create a new custom file object, containing a self assigned id (make it unique!),
     * the blocks id this custom file is referenced to and some attributes of
     * your choice
     *
     * @param string $id          an unique id for this custom file
     * @param int    $block_id    the id of the related block
     * @param array  $attributes  [description]
     */
    public function __construct($id = null, $block_id, $attributes = [])
    {
        $this->payload = [
            'id'         => $id,
            'block_id'   => $block_id,
            'attributes' => $attributes
        ];
    }

    /**
     * returns the payload: [
     *     'id'         => ...,
     *     'block_id'   => ...,
     *     'attributes' => [ ... ]
     * ]
     *
     * @return array the payload
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * get id for this custom file
     *
     * @return string custom file id
     */
    public function getId()
    {
        return $this->payload['id'];
    }

    /**
     * get id of related block
     *
     * @return int  related block id
     */
    public function getBlockId()
    {
        return $this->payload['block_id'];
    }

    /**
     * Overwrite the complete payload for this block. The payload MUST have the
     * following structure: [
     *     'id'         => ...,
     *     'block_id'   => ...,
     *     'attributes' => [ ... ]
     * ]
     *
     * @param array $payload  the payload of appropriate structure
     */
    public function setPayload($payload): void
    {
        if (!$payload['id']) {
            throw new InvalidArgumentException();
        }

        if (!$payload['block_id']) {
            throw new InvalidArgumentException();
        }

        $this->payload = $payload;
    }

    /**
     * Set the unique id for this custom file
     *
     * @param string $id
     */
    public function setId($id): void
    {
        $this->payload['id'] = $id;
    }

    /**
     * Set the id for the related block
     *
     * @param int $block_id
     */
    public function setBlockId($block_id): void
    {
        $this->payload['block_id'] = $block_id;
    }

    /**
     * Get the download url for this custom file
     *
     * @return string the download url
     */
    public function getDownloadUrl() : string
    {
        return rtrim(\URLHelper::getUrl('jsonapi.php/v1'), '/')
            . '/courseware-blocks/' . $this->payload['block_id']
            . '/custom-files/'. $this->payload['id'];
    }
}
