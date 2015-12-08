<?php
/**
 * @package loggerhead-app
 * @copyright Copyright © 2015 Danny Smart
 */
namespace Downsider\LoggerheadApp\Log;

interface LogFactoryInterface 
{

    /**
     * @param $format
     * @param array $data
     * @return Log
     */
    public function create($format, array $data = []);

} 