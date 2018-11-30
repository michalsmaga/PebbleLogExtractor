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
     * @throws Exception
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
     * @param DateTime $DateTime
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

        return [
            basename($this->fileName),
            $this->makeDateTime(),
            implode(',', $this->explodedData)
        ];
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
     * Determine if data row has data, not only 0.
     *
     * @return mixed
     */
    abstract public function containEmptyData();

    /**
     * Create date and time for current row.
     *
     * @return mixed
     */
    abstract protected function makeDateTime();
} 