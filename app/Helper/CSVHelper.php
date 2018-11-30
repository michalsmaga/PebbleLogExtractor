<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 15:31
 */

namespace PebbleLogExtractor\Helper;

/**
 * Class CSVHelper
 *
 * @package PebbleLogExtractor\Helper
 */
class CSVHelper
{

    /**
     * Create CSV file.
     *
     * @param array $data
     * @param string $path
     * @param bool $hasHeader
     */
    public static function exportToCsv(array $data, string $path, bool $hasHeader = false)
    {

        $handle = fopen($path, "w");

        if ($hasHeader) {

            fputcsv($handle, array_shift($data), ';');
        }

        foreach ($data as $row) {

            fputcsv($handle, $row, ';');
        }

        fclose($handle);
    }
} 