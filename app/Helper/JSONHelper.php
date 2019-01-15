<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 15:31
 */

namespace PebbleLogExtractor\Helper;

/**
 * Class JSONHelper
 *
 * @package PebbleLogExtractor\Helper
 */
class JSONHelper
{

    /**
     * Type of file we want to handle.
     *
     * @var string
     */
    protected static $fileType = 'json';

    /**
     * Create JSON file.
     *
     * @param array $data
     * @param string $path
     * @param bool $hasHeader
     */
    public static function export(array $data, string $path, bool $hasHeader = false)
    {

        if (!$hasHeader) {

            array_shift($data);
        }

        $path = self::checkFileExtension($path);
        $handle = fopen($path, "w");
        fwrite($handle, json_encode($data));
        fclose($handle);
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