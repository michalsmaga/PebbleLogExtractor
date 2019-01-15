<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 16:17
 */

namespace PebbleLogExtractor\Model;

/**
 * Class RowTypeOne
 *
 * @package PebbleLogExtractor\Model
 */
class RowTypeOne extends AbstractRow
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
         * Row full of junk.
         * Skipp it.
         */

        if (strpos($this->explodedRow[1], 'save') !== 0 || count($this->explodedRow) !== 3 || in_array($this->explodedRow[0], ['[ERROR]']))
        {

            return false;
        }

        /**
         * Three rows at the end of bank (logical part of log file).
         * Those rows are repeated in each bank, we don't need them.
         * Skipp it.
         */

        if (in_array(explode(',', $this->explodedRow[2])[0], [40, 41, 42])) {

            return false;
        }

        return true;
    }

    /**
     * Create DateTime Object from data.
     *
     * @return DateTime
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
     * Create date for current row.
     *
     * @return mixed
     */
    protected function makeDate()
    {

        return (clone $this->DateTime)->add(new \DateInterval('PT' . $this->explodedData[1] . 'M'))->format('Y-m-d');
    }

    /**
     * Create time for current row.
     *
     * @return mixed
     */
    protected function makeTime()
    {

        return (clone $this->DateTime)->add(new \DateInterval('PT' . $this->explodedData[1] . 'M'))->format('H:m');
    }

    /**
     * Return array of data without numbering and any other artificial information.
     *
     * @return array
     */
    protected function getCleanData() {

        array_shift($this->explodedData);
        array_shift($this->explodedData);

        $this->replaceBlankData();

        return $this->explodedData;
    }

    /**
     * Determine if row has useful data or blank records.
     *
     * @return bool
     */
    protected function hasBlankData() {

        $lastValue = end($this->explodedData);
        if ($lastValue == -1) {

            return false;
        }

        return true;
    }
}