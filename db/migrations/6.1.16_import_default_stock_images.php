<?php

class ImportDefaultStockImages extends Migration
{

    public function description()
    {
        return 'Imports the default stock image pool from an external ZIP file.';
    }

    public function up()
    {
        $zipFile = $GLOBALS['TMP_PATH'] . '/pool.zip';
        $extractPath = $GLOBALS['TMP_PATH'] . '/pool/';

        // Fetch zip with pool images.
        file_put_contents(
            $zipFile,
            file_get_contents('https://gitlab.studip.de/studip/bilderpool/-/raw/main/Stud.IP_5_Bilderpool.zip?ref_type=heads&inline=false')
        );

        // Unzip archive to temp directory.
        $zip = new ZipArchive();
        $res = $zip->open($zipFile);
        if ($res) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            unlink($zipFile);
            die('Could not open zip file.');
        }

        // Get metadata and create stock images if not already present.
        $csv_file = file($extractPath . 'meta.csv');
        if (!$csv_file) {
            $this->cleanup($extractPath);
            unlink($zipFile);
            die('No meta.csv file provided.');
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

        foreach ($images as $meta) {
            $filename = $meta['filename'];
            if (!$filename) {
                continue;
            }

            // Import file only if it doesn't already exist
            if (!StockImage::findOneByDescription($meta['description'] ?? 'STOCKIMAGE')) {
                $filepath = $extractPath . $filename;
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
            }
        }

        $this->cleanup($extractPath);
        unlink($zipFile);
    }

    private function cleanup($path)
    {
        $iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($iterator,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($path);
    }

}
