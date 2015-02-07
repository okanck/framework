<?php

namespace Service\Logger\Env;

use Obullo\Container\Container;
use Obullo\Log\LoggerServiceProvider;
use Obullo\ServiceProviders\ServiceInterface;

/**
 * Log Service
 *
 * @category  Service
 * @package   Logger
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2014 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/docs/services
 */
class Local implements ServiceInterface
{
    /**
     * Registry
     *
     * @param object $c container
     * 
     * @return void
     */
    public function register(Container $c)
    {
        $c['logger'] = function () use ($c) {

            $serviceProvider = new LoggerServiceProvider($c);  // $c['service provider logger']->get('driver' => 'queue')->getLogger();
            $logger = $serviceProvider->getLogger();
            /*
            |--------------------------------------------------------------------------
            | Register Filters
            |--------------------------------------------------------------------------
            */
            $logger->registerFilter('priority', 'Log\Filters\PriorityFilter');
            $logger->registerFilter('input', 'Log\Filters\InputFilter');
            /*
            |--------------------------------------------------------------------------
            | Register Handlers
            |--------------------------------------------------------------------------
            */
            $logger->registerHandler(5, 'file');  // Second paramter should register the class PATH.
            $logger->registerHandler(4, 'mongo')->filter('priority.notIn', array(LOG_DEBUG));
            $logger->registerHandler(3, 'email')->filter('priority.notIn', array(LOG_DEBUG));
            /*
            |--------------------------------------------------------------------------
            | Add Writers - Primary file writer should be available on local server
            |--------------------------------------------------------------------------
            */
            $logger->addWriter('file')->filter('priority.notIn', array(LOG_INFO));
            
            return $logger;
        };
    }
}

// END Local class

/* End of file Local.php */
/* Location: .classes/Service/Logger/Env/Local.php */