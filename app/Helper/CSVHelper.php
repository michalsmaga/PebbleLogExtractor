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
     * Type of file we want to handle.
     *
     * @var string
     */
    protected static $fileType = 'csv';

    /**
     * Create CSV file.
     *
     * @param array $data
     * @param string $path
     * @param string $delimiter
     * @param bool $hasHeader
     */
    public static function export(array $data, string $path, string $delimiter = ';', bool $hasHeader = false)
    {

        $path = self::checkFileExtension($path);
        $handle = fopen($path, "w");

        /**
         * Write headers.
         */
        if ($hasHeader) {

            fputcsv($handle, array_shift($data), $delimiter);
        }

        /**
         * Write data.
         */
        foreach ($data as $row) {

            fputcsv($handle, $row, $delimiter);
        }

        fclose($handle);
    }

    /**
     * Return content of given file as array.
     *
     * @param string $path
     * @return array
     */
    public static function getContent(string $path)
    {

        if (!is_file($path)) {

            return [];
        }

        return file($path);
    }

    /**
     * Add an extension if file dosn't have.
     *
     * @param string $path
     * @return string
     */
    protected static function checkFileExtension(string $path)
    {

        $info = pathinfo($path);

        if (!isset($info['extension'])) {

            $path .= '.' . self::$fileType;
        }

        return $path;
    }
}