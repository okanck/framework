<?php

namespace Log\Handlers\EmailHandler;

use Obullo\Log\Handler\FileHandler,
    Obullo\Log\Writer\QueueWriter;

/**
 * "EmailHandler" with "CartridgeQueueWriter"
 * 
 * @category  Log
 * @package   Handler
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2014 Obullo
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL Licence
 * @link      http://obullo.com/package/log
 */
Class CartridgeQueueWriter
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Handler closure
     * 
     * @var object
     */
    protected $closure;

    /**
     * Constructor
     * 
     * @param object $c container
     */
    public function __construct($c)
    {
        $this->closure = function () use ($c) {

            return new EmailHandler(
                $c,
                new QueueWriter(
                    $c->load('service/queue'),
                    array(
                        'channel' =>  LOGGER_CHANNEL,
                        'route' => gethostname(). LOGGER_NAME .'Email',
                        'job' => LOGGER_JOB,
                        'delay' => 0,
                        'format' => array(
                            'context' => 'array',  // json
                            'extra'   => 'array'   // json
                        ),
                    )
                )
            );
        };
    }

    /**
     * Returns to closure data of handler
     * 
     * @return object closure
     */
    public function getHandler()
    {
        return $this->closure;
    }
}

// END CartridgeQueueWriter class

/* End of file CartridgeQueueWriter.php */
/* Location: .app/Log/Handlers/FileHandler/CartridgeQueueWriter.php */