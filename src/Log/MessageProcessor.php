<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Log;

use Downsider\Loggerhead\Exception\LogProcessingException;
use Downsider\Loggerhead\Format\FormatRepository;
use Downsider\Loggerhead\Format\Format;

class MessageProcessor 
{
    /**
     * @var FormatRepository
     */
    protected $formatRepo;

    /**
     * @var LogFactoryInterface
     */
    protected $logFactory;

    /**
     * @var Format
     */
    protected $format;

    /**
     * @var Log
     */
    protected $log;

    public function __construct(FormatRepository $formatRepo, LogFactoryInterface $logFactory)
    {
        $this->formatRepo = $formatRepo;
        $this->logFactory = $logFactory;
    }

    public function process($messageJson)
    {
        // reset state
        $this->format = null;
        $this->log = null;

        // decode and validate the message
        $message = json_decode($messageJson, true);
        if (empty($message)) {
            throw new LogProcessingException("The received message was invalid: " . json_last_error_msg(), "Raw message:", $messageJson);
        }

        if (empty($message["format"]) || empty($message["entry"])) {
            throw new LogProcessingException("The received message was not in the correct format", "Both 'format' and 'entry' fields are required");
        }

        // try to load the format from the database
        $format = $message["format"];
        $formatList = $this->formatRepo->filter(["name" => $format]);
        if (empty($formatList[0])) {
            throw new LogProcessingException("Format '$format' not found");
        }

        $this->format = $formatList[0];
        $this->log = $this->logFactory->create($this->format->getName(), $message["entry"]);

    }

    /**
     * @return Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return Log
     */
    public function getLog()
    {
        return $this->log;
    }

} 