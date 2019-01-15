<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 16:03
 */

namespace PebbleLogExtractor\Model;


use PebbleLogExtractor\Helper;

/**
 * Class File
 *
 * @package PebbleLogExtractor\Model
 */
class CSVDate
{

    /**
     * Key => value pairs.
     * File id => start date.
     *
     * @var array
     */
    protected $dates = [];

    public function __construct()
    {

        $csvDates = Helper\CSVHelper::getContent(Helper\ConfigHelper::get('paths.date_csv'));

        if (empty($csvDates)) {

            return;
        }

        /**
         * Remove header.
         */

        unset($csvDates[0]);

        foreach ($csvDates as $row) {

            $row = explode(';', $row);
            $matches = [];
            preg_match('/\d+/', $row[0], $matches);
            $this->dates[$matches[0]] = trim($row[1]);
        }
    }

    /**
     * Return date for given file name.
     *
     * @param string $fileName
     * @return null|\DateTime
     */
    public function getDateForFile(string $fileName)
    {

        $ids = [];
        preg_match('/\d+/', basename($fileName), $ids);

        if (empty($id) && isset($this->dates[$ids[0]])) {

            return \DateTime::createFromFormat('Y-m-d', $this->dates[$ids[0]]);
        }

        return null;
    }
}