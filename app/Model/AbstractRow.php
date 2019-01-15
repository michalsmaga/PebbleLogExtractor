<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 22.11.18
 * Time: 14:13
 */

namespace PebbleLogExtractor\Model;

/**
 * Class AbstractRow
 *
 * @package PebbleLogExtractor\Model
 */
abstract class AbstractRow
{

    /**
     * Data delimiter.
     *
     * @var string
     */
    protected $delimiter = " ";

    /**
     * Row as array.
     *
     * @var array
     */
    protected $explodedRow = [];

    /**
     * Pure data as array.
     *
     * @var array
     */
    protected $explodedData = [];

    /**
     * Number of current row in file.
     *
     * @var int
     */
    protected $rowNumber = 0;

    /**
     * Name of source file.
     *
     * @var null
     */
    protected $fileName = null;

    /**
     * DateTime object. When logging to file was start.
     *
     * @var null
     */
    protected $DateTime = null;

    /**
     * Simple constructor.
     *
     * @param string $row
     * @param int $rowNumber
     *
     * @throws \Exception
     */
    public function __construct(string $row, int $rowNumber)
    {

        $this->rowNumber = $rowNumber;
        $this->explodedRow = explode($this->delimiter, trim($row));

        if (count($this->explodedRow) > 1) {

            $this->explodedData = $this->extractData();
        } else {

            throw new \Exception("Row {$this->rowNumber} is empty in file {$this->fileName}.");
        }
    }

    /**
     * Set filename
     *
     * @param string $fileName
     */
    public function setFileName(string $fileName)
    {

        $this->fileName = $fileName;
    }

    /**
     * Set DateTime.
     *
     * @param \DateTime $DateTime
     */
    public function setDateTime(\DateTime $DateTime)
    {

        $this->DateTime = $DateTime;
    }

    /**
     * Format and return data from row.
     *
     * @return mixed
     */
    public function reformatRow()
    {

        $data = [
            basename($this->fileName),
            $this->makeDate(),
            $this->makeTime()
        ];
        return array_merge($data, $this->getCleanData());
    }

    /**
     * Return number of row in file.
     *
     * @return int
     */
    public function getRowNumber()
    {

        return $this->rowNumber;
    }

    /**
     * Determine if data row has an useful data.
     *
     * @return mixed
     */
    public function containEmptyData()
    {

        /**
         * Ex.'6,17099,0,0,0,-1'    - row with '-1' at the end - this questionnaire was skipped. We need those rows.
         * Ex.'0    0    255    0    0'  - row with '255' at the middle position - this questionnaire was skipped. We need those rows.
         * Ex.'0,1,0,0,0,0'         - row with only '0's with minutes (number on second position) - we don't know what it means. We need those rows.
         * Ex.'13,0,0,0,0,0'        - row with only '0's without minutes (0 on second position) - questionnaires "showed" after Pebble clock was turn off. Skipp those rows.
         */

        $flag = true;

        foreach ($this->explodedData as $key => $value) {

            if ($key === 0 || ($key === 1 && $value !== 0)) {

                continue;
            }

            if ($value != 0) {

                $flag = false;
            }
        }

        return $flag;
    }

    /**
     * Replace blank data to 999,999,999,999.
     */
    protected function replaceBlankData()
    {

        if (!$this->hasBlankData()) {

            $this->explodedData = [999, 999, 999, 999];
        }
    }

    /**
     * Pick pure data from row array and return them.
     *
     * @return mixed
     */
    abstract protected function extractData();

    /**
     * Determine if row contains useful data.
     *
     * @return bool
     */
    abstract public function checkIfUseful();

    /**
     * Create DateTime Object from data.
     *
     * @return DateTime
     */
    abstract public function getDateTime();

    /**
     * Create date for current row.
     *
     * @return mixed
     */
    abstract protected function makeDate();

    /**
     * Create time for current row.
     *
     * @return mixed
     */
    abstract protected function makeTime();

    /**
     * Return array of data without numbering and any other artificial information.
     *
     * @return array
     */
    abstract protected function getCleanData();

    /**
     * Determine if row has useful data or blank records.
     *
     * @return bool
     */
    abstract protected function hasBlankData();

} 