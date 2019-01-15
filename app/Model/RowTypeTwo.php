<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 16:17
 */

namespace PebbleLogExtractor\Model;

/**
 * Class RowTypeTwo
 *
 * @package PebbleLogExtractor\Model
 */
class RowTypeTwo extends AbstractRow
{

    /**
     * Row delimiter.
     *
     * @var string
     */
    protected $delimiter = "\t";

    /**
     * Pick pure data from row array and return them.
     *
     * @return mixed
     */
    protected function extractData()
    {

        return [
            $this->explodedRow[2],
            $this->explodedRow[3],
            $this->explodedRow[4],
            $this->explodedRow[5],
            $this->explodedRow[6]
        ];
    }

    /**
     * Determine if row contains useful data.
     *
     * @return bool
     */
    public function checkIfUseful()
    {

        return true;
    }

    /**
     * Create DateTime Object from data.
     *
     * @return DateTime
     */
    public function getDateTime()
    {

        return \DateTime::createFromFormat('d.m.Y, H:i:s', $this->explodedRow[1]);
    }

    /**
     * Create date for current row.
     *
     * @return mixed
     */
    protected function makeDate()
    {

        return \DateTime::createFromFormat('d.m.Y, H:i:s', $this->explodedRow[1])->format('Y-m-d');
    }

    /**
     * Create time for current row.
     *
     * @return mixed
     */
    protected function makeTime()
    {

        return \DateTime::createFromFormat('d.m.Y, H:i:s', $this->explodedRow[1])->format('H:m');
    }

    /**
     * Return array of data without numbering and any other artificial information.
     *
     * @return array
     */
    protected function getCleanData()
    {

        array_shift($this->explodedData);

        $this->replaceBlankData();

        return $this->explodedData;
    }

    /**
     * Determine if row has useful data or blank records.
     *
     * @return bool
     */
    protected function hasBlankData()
    {

        if ($this->explodedData[1] == 255) {

            return false;
        }

        return true;
    }
}