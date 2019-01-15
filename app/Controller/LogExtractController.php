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

        $CSVDate = new Model\CSVDate();

        if (!$DataHelper->hasFiles()) {

            $message = "There is no log files in {$inputPath} directory.";
            Helper\LogHelper::getInstance()->addMessage($message);

            die($message . '</br>');
        }

        $data = [];
        foreach ($DataHelper->getFiles() as $file) {

            try {

                $DateTime = $CSVDate->getDateForFile($file);
                $File = new Model\File($file, $DateTime);
                $data = array_merge($data, $File->extractData());
            } catch (\Exception $e) {

                Helper\LogHelper::getInstance()->addMessage($e->getMessage());
            }
        }

        $this->export($data);

        Helper\LogHelper::getInstance()->write();
    }

    /**
     * Export extracted data to output file.
     *
     * @param array $data
     */
    protected function export(array $data)
    {

        switch (Helper\ConfigHelper::get('output_file.format')) {

            case 'json':

                Helper\JSONHelper::export($data, Helper\ConfigHelper::get('output_file.file_name'));
                break;
            case 'csv':

                Helper\CSVHelper::export($data, Helper\ConfigHelper::get('output_file.file_name'));
            default:
        }
    }
}