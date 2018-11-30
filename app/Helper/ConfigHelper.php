<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 22.11.18
 * Time: 11:01
 */

namespace PebbleLogExtractor\Helper;


/**
 * Class ConfigHelper
 *
 * @package PebbleLogExtractor\Helper
 */
class ConfigHelper
{

    /**
     * Get value from config file.
     * Key must be given in dot notation.
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {

        $config = include(__DIR__ . '/../Config/Config.php');
        $explodedKey = explode('.', $key);

        foreach ($explodedKey as $key) {

            $config = $config[$key];
        }

        return $config;
    }
}