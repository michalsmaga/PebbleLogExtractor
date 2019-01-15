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
class File
{

    /**
     * Name of file.
     *
     * @var null
     */
    protected $fileName = null;

    /**
     * File content stored as an array.
     *
     * @var array
     */
    protected $contentArray = [];

    /**
     * DateTime object. Point in time where first data appears in file.
     *
     * @var bool
     */
    protected $DateTime = false;

    /**
     * Type of file.
     *
     * @var int
     */
    protected $fileType = 1;

    /**
     * Number of first line with data.
     *
     * @var int
     */
    protected $dataFirstRowIdx = 0;

    /**
     * Number of last line with data.
     *
     * @var int
     */
    protected $dataLastRowIdx = 0;


    /**
     * Simple constructor.
     *
     * @param string $file
     * @param \DateTime $DateTime
     *
     * @throws \Exception
     */
    public function __construct(string $file, \DateTime $DateTime = null)
    {

        if (!file_exists($file)) {

            throw new \Exception("File {$this->fileName} is not exists.");
        }

        $this->fileName = $file;

        $this->createContentAsArray();
        $this->determineFileType();
        $this->setDateTime($DateTime);
        $this->removeRowsWithoutData();
    }

    /**
     * Parse to array a content of file.
     *
     * @throws Exception
     */
    protected function createContentAsArray()
    {

        $this->contentArray = file($this->fileName);

        if (count($this->contentArray) == 0) {

            throw new \Exception("File {$this->fileName} don't contain any data.");
        }
    }

    /**
     * Set DateTime property.
     *
     * @param \DateTime $DateTime
     */
    protected function setDateTime(\DateTime $DateTime)
    {

        if (!is_null($DateTime)) {

            $this->DateTime = $DateTime;
        } else {

            $this->createDateTime();
        }
    }

    /**
     * Create DateTime object for file.
     *
     * @throws Exception
     */
    protected function createDateTime()
    {

        foreach ($this->contentArray as $key => $row) {

            if (!$this->DateTime) {

                $Row = Row::getInstance($row, $key, $this->fileType);
                $this->DateTime = $Row->getDateTime();
            } else {

                return;
            }
        }

        throw new \Exception("File {$this->fileName} don't contain date and time data.");
    }

    /**
     * Determine type of file by given row.
     * 1 - CloudPebble_XXXX
     * 2 - gos-XXXX
     * 3 - XXXX_mpp
     */
    protected function determineFileType()
    {

        if (preg_match('/^\[ERROR\]|\[INFO\]|\[DEBUG\]/', $this->contentArray[0]) === 1) {

            $this->fileType = 1;
        } else {
            if (preg_match('/^\d+_mpp/', $this->contentArray[0]) === 1) {

                $this->fileType = 2;
            } else {

                $this->fileType = 3;
            }
        }
    }

    /**
     * Remove rows with technical information.
     */
    protected function removeRowsWithoutData()
    {

        $toRemove = [];
        foreach ($this->contentArray as $key => $row) {

            try {

                $Row = Row::getInstance($row, $key, $this->fileType);
                if (!$Row->checkIfUseful()) {

                    $toRemove[] = $key;
                }
            } catch (\Exception $e) {

                $toRemove[] = $key;
            }
        }

        foreach ($toRemove as $key) {

            unset($this->contentArray[$key]);
        }

        $this->contentArray = array_values($this->contentArray);
    }

    /**
     * Extract and format data from file.
     *
     * @return array
     */
    public function extractData()
    {

        $this->setDataPosition();
        $data = [];

        foreach ($this->contentArray as $key => $row) {

            try {

                $Row = Row::getInstance($row, $key, $this->fileType);

                if (!$Row->checkIfUseful() || $this->checkRowToSkipp($Row)) {

                    continue;
                }

                $Row->setFileName($this->fileName);
                $Row->setDateTime($this->DateTime);
                $data[$key] = $Row;
            } catch (\Exception $e) {

                continue;
            }
        }

        return $this->reformat($data);
    }

    /**
     * Set index of first and last row containing data.
     */
    protected function setDataPosition()
    {

        $totalRows = count($this->contentArray);
        $lastPositionIdx = false;

        foreach ($this->contentArray as $key => $row) {

            try {

                $Row = Row::getInstance($row, $key, $this->fileType);
            } catch (\Exception $e) {

                continue;
            }

            if ($Row->containEmptyData() === false) {

                if ($lastPositionIdx === false) {

                    $this->dataFirstRowIdx = $key;
                }

                $lastPositionIdx = $key;
            }
        }

        $this->dataLastRowIdx = $totalRows > $lastPositionIdx ? ($lastPositionIdx + 1) : $lastPositionIdx;
    }

    /**
     * Check if row has no data and if it should be ommited.
     *
     * @param AbstractRow $Row
     * @return bool
     */
    protected function checkRowToSkipp(AbstractRow $Row)
    {

        $fileCleaningConf = Helper\ConfigHelper::get('files.' . $this->fileType);

        if (!$Row->containEmptyData()) {

            return false;
        }

        if (
            (
                $Row->getRowNumber() < $this->dataFirstRowIdx &&
                $fileCleaningConf['remove_before_data']
            ) || (
                $this->dataFirstRowIdx < $Row->getRowNumber() &&
                $Row->getRowNumber() > $this->dataFirstRowIdx &&
                $fileCleaningConf['remove_between_data']
            ) || (
                $Row->getRowNumber() > $this->dataFirstRowIdx &&
                $fileCleaningConf['remove_after_data']
            )
        ) {

            return true;
        }

        return false;
    }

    /**
     * Prepare row to return. Add date and filename to it.
     *
     * @param array $data
     * @return array
     */
    protected function reformat(array $data)
    {

        $newData = [];
        foreach ($data as $Row) {

            $newData[] = $Row->reformatRow();
        }

        return $newData;
    }
}