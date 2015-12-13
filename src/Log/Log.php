<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Log;

use Downsider\Clay\Model\ModelTrait;

class Log
{
    use ModelTrait;

    protected $format;

    protected $entry;

    public function __construct(array $data = [])
    {
        $this->setEntry($data);
    }

    /**
     * @param array $data
     */
    public function setEntry(array $data)
    {
        $this->entry = $data;
    }

    /**
     * @return array
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getEntry();
    }

    public function getId()
    {
        return isset($this->entry["id"])? $this->entry["id"]: null;
    }
} 