<?php
/**
 * @package loggerhead-app
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\LoggerheadApp\Log;

class LogFactory implements LogFactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function create($format, array $data = [])
    {
        $log = new Log($data);
        $log->setFormat($format);
        return $log;
    }

} 