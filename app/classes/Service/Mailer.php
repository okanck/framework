<?php

namespace Service;

use Obullo\Mail\QueueMailer;

/**
 * Mailer Service
 *
 * @category  Service
 * @package   Mail
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2014 Obullo
 * @license   http://opensource.org/licenses/MIT
 * @link      http://obullo.com/docs/services
 */
Class Mailer implements ServiceInterface
{
    /**
     * Registry
     *
     * @param object $c container
     * 
     * @return void
     */
    public function register($c)
    {
        $c['mailer'] = function () use ($c) {
            return new QueueMailer($c, $c['config']['mail']);
        };
    }
}

// END Mailer class

/* End of file Mailer.php */
/* Location: .classes/Service/Mailer.php */