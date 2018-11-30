<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 16:17
 */

namespace PebbleLogExtractor\Model;

/**
 * Class RowTypeThree
 *
 * @package PebbleLogExtractor\Model
 */
class RowTypeThree extends AbstractRow
{

    /**
     * Row delimiter.
     *
     * @var string
     */
    protected $delimiter = " ";

    /**
     * Pick pure data from row array and return them.
     *
     * @return mixed
     */
    protected function extractData()
    {

        return explode(',', $this->explodedRow[2]);
    }

    /**
     * Determine if row contains useful data.
     *
     * @return bool
     */
    public function checkIfUseful()
    {

        /**
         * Or row full of junk.
         * Skipp it.
         */

        if (strpos($this->explodedRow[1], 'save.c:138') !== 0 || count($this->explodedRow) !== 3) {

            return false;
        }

        /**
         * Row with some strange data.
         * Skipp it.
         */

        if (explode(',', $this->explodedRow[2])[0] == 42) {

            return false;
        }

        return true;
    }

    /**
     * Create DateTime Object from data.
     *
     * @return \DateTime
     */
    public function getDateTime()
    {

        if ($this->explodedRow[2] === 'base' && $this->explodedRow[3] === 'D:H:M:') {

            $explodedDate = explode(':', $this->explodedRow[4]);
            $date = \DateTime::createFromFormat('z Y', strval($explodedDate[0]) . ' 2018');

            return $date->setTime($explodedDate[0], $explodedDate[1]);
        }

        return false;
    }

    /**
     * Determine if data row has data, not only 0.
     *
     * @return mixed
     */
    public function containEmptyData()
    {

        return end($this->explodedData) == -1;
    }

    /**
     * Create date and time for current row.
     *
     * @return mixed
     */
    protected function makeDateTime()
    {

        return (clone $this->DateTime)->add(new \DateInterval('PT' . $this->explodedData[1] . 'M'))->format('Y-m-d H:m');
    }
}