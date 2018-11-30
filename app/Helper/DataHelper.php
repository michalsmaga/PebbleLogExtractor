<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 15:39
 */

namespace PebbleLogExtractor\Helper;

/**
 * Class DataHelper
 *
 * @package PebbleLogExtractor\Helper
 */
class DataHelper
{

    /**
     * Path to source directory.
     *
     * @var null|string|string
     */
    protected $path = null;

    /**
     * List of files with log extension.
     *
     * @var array
     */
    protected $files = [];

    /**
     * Public constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {

        $this->path = $path;

        $this->getFilesList();
    }

    /**
     * Create a list of *.log files in current directory.
     *
     * @return array
     */
    protected function getFilesList()
    {

        if (!file_exists($this->path)) {

            $this->files = [];
        }

        $files = glob($this->path . DIRECTORY_SEPARATOR . '*.log');
        if ($files !== false && !empty($files)) {

            $this->files = $files;
        }
    }

    /**
     * Return list of files in current location.
     *
     * @return array
     */
    public function getFiles()
    {

        return $this->files;
    }

    /**
     * Check if log files are present in current path.
     *
     * @return bool
     */
    public function hasFiles()
    {

        return (bool)count($this->files);
    }
}