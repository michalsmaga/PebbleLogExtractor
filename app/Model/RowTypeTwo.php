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

        return \DateTime::createFromFormat('d.m.Y, H:i:s', $this->explodedRow[1])->format('Y-m-d H:m');
    }
}