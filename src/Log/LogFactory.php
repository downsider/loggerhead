<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Log;

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