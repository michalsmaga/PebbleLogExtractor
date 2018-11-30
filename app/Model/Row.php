<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 22.11.18
 * Time: 15:26
 */

namespace PebbleLogExtractor\Model;

/**
 * Class Row
 *
 * @package PebbleLogExtractor\Model
 */
class Row
{

    /**
     * Create and return Row objext of given type.
     *
     * @param string $row
     * @param int $rowNumber
     * @param int $fileType
     * @return RowTypeOne|RowTypeThree|RowTypeTwo
     */
    public static function getInstance(string $row, int $rowNumber, int $fileType)
    {

        switch ($fileType) {

            case 1:

                return new RowTypeOne($row, $rowNumber);
            case 2:

                return new RowTypeTwo($row, $rowNumber);
            case 3:

                return new RowTypeThree($row, $rowNumber);
        }
    }
}