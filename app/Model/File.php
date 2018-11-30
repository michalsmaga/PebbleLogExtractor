<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 21.11.18
 * Time: 16:03
 */

namespace PebbleLogExtractor\Model;

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
     * Simple constructor.
     *
     * @param $file
     *
     * @throws Exception
     */
    public function __construct($file)
    {

        if (!file_exists($file)) {

            throw new \Exception("File {$this->fileName} is not exists.");
        }

        $this->fileName = $file;

        $this->createContentAsArray();
        $this->determineFileType();
        $this->createDateTime();
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
     * Extract and format data from file.
     *
     * @return array
     */
    public function extractData()
    {

        $data = [];
        $idxRowWithData = 0;

        foreach ($this->contentArray as $key => $row) {

            try {

                $Row = Row::getInstance($row, $key, $this->fileType);

                if (!$Row->checkIfUseful()) {

                    continue;
                }

                if ($Row->containEmptyData()) {

                    continue;
                } else {

                    $Row->setFileName($this->fileName);
                    $Row->setDateTime($this->DateTime);
                    $data[$key] = $Row;
                    $idxRowWithData = $key;
                }
            } catch (\Exception $e) {

                continue;
            }
        }

        $data = $this->cleanExtractedData($data, $idxRowWithData);

        return $this->reformat($data);
    }

    /**
     * Remove items with key greater then given in $highestIdx from given table.
     *
     * @param array $data
     * @param int $highestIdx
     * @return array
     */
    protected function cleanExtractedData(array $data, int $highestIdx)
    {

        foreach ($data as $key => $value) {

            if ($key > $highestIdx) {

                unset($data[$key]);
            }
        }

        return array_values($data);
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