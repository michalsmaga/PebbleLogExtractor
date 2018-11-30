<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 22.11.18
 * Time: 11:01
 */

namespace PebbleLogExtractor\Helper;

/**
 * Class LogHelper
 *
 * @package PebbleLogExtractor\Helper
 */
class LogHelper
{

    /**
     * Instance of self.
     *
     * @var
     */
    private static $instance;

    /**
     * Stored messages.
     *
     * @var array
     */
    private $messages = [];

    /**
     * Private constructor.
     */
    private function __construct()
    {
    }

    /**
     * Return an instance of self.
     *
     * @return LogHelper
     */
    public static function getInstance()
    {

        if (self::$instance === null) {

            self::$instance = new LogHelper();
        }

        return self::$instance;
    }

    /**
     * Add message to log.
     *
     * @param string $message
     * @param array $channel
     */
    public function addMessage(string $message, array $channel = [])
    {

        if (empty($channel)) {

            $this->pushToDefault($message);
        } else {

            $this->pushByKeys($message, $channel);
        }
    }

    /**
     * Push message to default log channel.
     *
     * @param string $message
     */
    protected function pushToDefault(string $message)
    {

        $this->messages['default'][] = $this->prepareDateTime() . ' ' . $message;
    }

    /**
     * Push message to specified log channel.
     *
     * @param string $message
     * @param array $channel
     */
    protected function pushByKeys(string $message, array $channel = [])
    {

        $destination = & $this->messages;
        foreach ($channel as $key) {

            if (isset($destination[$key])) {

                $destination = & $destination[$key];
            } else {

                $destination[$key] = [];
                $destination = & $destination[$key];
            }
        }
        $destination[] = $this->prepareDateTime() . ' ' . $message;
    }

    /**
     * Create date time string.
     *
     * @return string
     */
    protected function prepareDateTime()
    {

        $DateTime = new \DateTime();
        return $DateTime->format('Y-m-d H:m:s');
    }

    /**
     * Show stored log messages.
     */
    public function showAll()
    {

        $this->messages = $this->formatMessages($this->messages);

        echo '<pre>';
        print_r($this->messages);
        echo '<pre/>';
    }

    /**
     * Sort given array.
     *
     * @param array $messages
     * @return array
     */
    protected function formatMessages($messages)
    {

        if (empty($messages)) {

            return $messages;
        }

        $channels = [];
        foreach ($messages as $key => $messagesPart) {

            if (!is_int($key)) {

                $messagesPart = $this->formatMessages($messagesPart);
                $channels[$key] = $messagesPart;
                unset($messages[$key]);
            }
        }

        if (!empty($channels)) {

            ksort($channels);
            $messages = array_merge($channels, $messages);
        }

        return $messages;
    }

    /**
     * Write log file.
     * Use messages stored in channel or all messages if channel is not specified.
     *
     * @param string $channel
     * @return array
     */
    public function write(string $channel = null)
    {

        $fileHandle = fopen(ConfigHelper::get('paths.logFile'), 'w');

        if (is_null($channel)) {

            if (count($this->messages) > 0) {

                foreach ($this->messages as $channel => $data) {

                    $this->writeForChannel($channel, $fileHandle);
                }
            }
        } elseif (isset($this->messages[$channel])) {

            $this->writeForChannel($channel, $fileHandle);
        }

        fclose($fileHandle);
    }

    /**
     * Write logs to file for given channel.
     *
     * @param string $channel
     * @param $fileHandle
     */
    protected function writeForChannel(string $channel, $fileHandle)
    {

        if (count($this->messages[$channel]) > 0) {

            foreach ($this->messages[$channel] as $row) {

                fwrite($fileHandle, '[' . $channel . '] ' . $row);
            }
        }
    }
} 