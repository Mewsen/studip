<?php
namespace Studip;

/**
 * Custom derived ZipArchive class with convenience methods for
 * zip archive handling.
 *
 * This replaces the before-used PCLZip vendor library.
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since   Stud.IP 4.0
 */
class ZipArchive extends \ZipArchive
{
    /**
     * @var string encoding for filenames in zip
     */
    protected $output_encoding = 'UTF-8';

    public function getOutputEncoding()
    {
        return $this->output_encoding;
    }

    public function setOutputEncoding($string)
    {
        return $this->output_encoding = $string;
    }

    /**
     * Create and open an archive. Will add .zip extension if missing.
     *
     * @static
     *
     * @param String $filename Name of the zip archive
     *
     * @param bool $force_zip_extension Specifies whether the resulting ZIP file
     *     shall have the .zip file extension (true) or not (false).
     *     Defaults to false.
     *
     * @return static
     */
    public static function create($filename, $force_zip_extension = false)
    {
        if ($force_zip_extension) {
            if (mb_strtolower(mb_substr($filename, -3)) !== 'zip') {
                $filename = $filename . '.zip';
            }
        }

        $archive = new self();
        $archive->open($filename, self::CREATE);
        return $archive;
    }

    /**
     * Tests whether a zip archive is not corrupted.
     *
     * @static
     * @param String $filename Name of the zip archive
     * @return bool indicating whether the archive is not corrupted
     */
    public static function test($filename)
    {
        $archive = new self();
        $result = $archive->open($filename, self::CHECKCONS);

        if ($result === true) {
            $archive->close();
            return true;
        }

        return false;
    }

    /**
     * Extracts a zip archive to a certain path. Filenames will be
     * converted during this process. Malicious items containing ../
     * will be excluded.
     *
     * @static
     * @param String $filename Name of the zip archive
     * @param String $path Local path to extract to
     * @return bool indicating whether the archive could be extracted.
     * @todo A little more error checking would be nice.
     */
    public static function extractToPath($filename, $path)
    {
        $path = rtrim($path, '/') . '/';

        $archive = new self();
        $result = $archive->open($filename);

        if ($result !== true) {
            return false;
        }
        $ok = true;
        for ($i = 0; $i < $archive->numFiles; $i += 1) {
            $zip_filename = $archive->getNameIndex($i, self::FL_UNCHANGED);
            $filename = $archive->convertArchiveFilename($zip_filename);
            if (mb_strpos($filename, '../') !== false) {
                continue;
            }
            if (mb_substr($zip_filename, -1) === '/') {
                $dirname = trim($filename, '/');
            } else {
                $dirname = trim(dirname($filename), '/');
            }
            if ($dirname && $dirname !== '.') {
                if (!is_dir($path . $dirname)) {
                    if (mkdir($path . $dirname, 0777, true) === false) {
                        $ok = false;
                    }
                }
            }
            if (mb_substr($zip_filename, -1) === '/') {
                continue;
            }
            $source = $archive->getStream($zip_filename);
            $target = @fopen($path . $filename, 'wb+');
            if (@stream_copy_to_stream($source, $target) === false) {
                $ok = false;
            }
            @fclose($source);
            @fclose($target);
        }
        $archive->close();
        return $ok;
    }

    /**
     * Adds all files from a certain path.
     *
     * @param String $path Path name to add
     * @return array of local filenames
     * @uses ZipArchive::addFile
     */
    public function addFromPath($path, $folder = '')
    {
        $result = [];

        $files = glob(rtrim($path, '/') . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $result = array_merge(
                    $result,
                    $this->addFromPath($file, $folder . basename($file) . '/')
                );
            } elseif ($this->addFile($file, $folder . basename($file))) {
                $result[] = $this->convertLocalFilename($folder . basename($file));
            }
        }
        return array_filter($result);
    }

    /**
     * Adds a single file.
     *
     * @param string $filepath  Name of the file to add
     * @param ?string $entryname Name of the file inside the archive,
     *                           will default to $filename
     * @param int    $start     Unused but required (according to php doc)
     * @param int    $length    Unused but required (according to php doc)
     * @param int    $flags     Bitmask (see https://php.net/ziparchive.addfile)
     */
    public function addFile(
        string $filepath,
        ?string $entryname = null,
        int $start = 0,
        int $length = 0,
        int $flags = self::FL_OVERWRITE
    ): bool {
        $localname = $this->convertLocalFilename($entryname ?: basename($filepath));
        return parent::addFile($filepath, $localname, $start, $length, $flags);
    }

    /**
     * Converts the filename to a format that a zip file should be able
     * to handle.
     *
     * @param String $filename Name of the input file
     * @return String containing the converted filename
     */
    public function convertLocalFilename($filename)
    {
        if ($this->output_encoding !== 'UTF-8') {
            return iconv('UTF-8', $this->output_encoding . '//TRANSLIT', $filename);
        } else {
            return $filename;
        }
    }

    /**
     * Converts the filename from a format that a zip file should be able
     * to handle.
     *
     * @param String $filename Name of the input file from the archive
     * @return String containing the converted filename
     */
    public function convertArchiveFilename($filename)
    {
        if (!mb_detect_encoding($filename, 'UTF-8', true)) {
            return mb_convert_encoding($filename, $this->output_encoding, 'CP850');
        } else {
            return $filename;
        }
    }
}
