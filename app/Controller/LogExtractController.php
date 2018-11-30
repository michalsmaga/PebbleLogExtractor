<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 15.10.18
 * Time: 15:37
 */

namespace PebbleLogExtractor\Controller;


use PebbleLogExtractor\Helper;
use PebbleLogExtractor\Model;

/**
 * Class LogExtractController
 *
 * @package PebbleLogExtractor\Controller
 */
class LogExtractController
{

    /**
     * Method to execute action.
     */
    public function run()
    {

        $inputPath = Helper\ConfigHelper::get('paths.inputPath');
        $DataHelper = new Helper\DataHelper($inputPath);

        if (!$DataHelper->hasFiles()) {

            $message = "There is no log files in {$inputPath} directory.";
            Helper\LogHelper::getInstance()->addMessage($message);

            die($message . '</br>');
        }

        $filteredData = [];
        foreach ($DataHelper->getFiles() as $file) {

            try {

                $File = new Model\File($file);
                $filteredData = array_merge($filteredData, $File->extractData());
            } catch (\Exception $e) {

                Helper\LogHelper::getInstance()->addMessage($e->getMessage());
            }
        }

        Helper\CSVHelper::exportToCsv($filteredData, Helper\ConfigHelper::get('paths.outputFile'));
        Helper\LogHelper::getInstance()->write();
    }
}