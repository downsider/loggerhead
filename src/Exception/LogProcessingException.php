<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Exception;

class LogProcessingException extends \Exception
{

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $data;

    /**
     * @param string $message
     * @param string $comment
     * @param string $data
     */
    public function __construct($message, $comment = "", $data = "")
    {
        $this->comment = $comment;
        $this->data = $data;
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

}