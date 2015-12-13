<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */
namespace Downsider\Loggerhead\Log;

interface LogFactoryInterface 
{

    /**
     * @param $format
     * @param array $data
     * @return Log
     */
    public function create($format, array $data = []);

} 