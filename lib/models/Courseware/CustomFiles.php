<?php

namespace Courseware;

use Courseware\Filesystem\CustomFile;

/**
 * This interface enables a courseware-block to have a user defined representation
 * of files. This enables a block to have its own internal representation for
 * arbitrary content as well allowing the import and export of said content in
 * a defined and coherent way.
 */
interface CustomFiles
{
    /**
     * Returns an array of CustomFile objects belongig to this block
     *
     * @return array<int, CustomFile>
     */
    public function getCustomFiles() : array;

    /**
     * create a new custom file, the contents have to be set by updateCustomFilesContent afterwards
     *
     * @param  array     $metadata   any additional metadata needed, like id
     * @param  string    $content    the files contets
     *
     * @return CustomFile            the newly created custom file
     */
    public function createCustomFile(CustomFile $custom_file) : CustomFile;

    /**
     * returns the contents for the custom file with the passed id
     *
     * @param  string   $id         the id for the custom file
     *
     * @return string
     */
    public function readCustomFile($id) : string;

    /**
     * update the attributes of the custom file for the passed id
     *
     * @param  string     $id
     * @param  array      $metadata
     *
     * @return CustomFile
     */
    public function updateCustomFileMetadata($id, CustomFile $custom_file) : CustomFile;

    /**
     * update the contents of the customf ile for the passed id
     *
     * @param  string     $id
     * @param  string     $content
     *
     * @return CustomFile
     */
    public function updateCustomFileContent($id, $content) : CustomFile;

    /**
     * delete the custom file for the passed id
     *
     * @param  string     $id
     *
     * @return bool
     */
    public function deleteCustomFile($id) : bool;
}
