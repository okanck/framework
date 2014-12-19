<?php

namespace Service;

use Obullo\Permissions\RbacService;

/**
 * Rbac PermissionService
 *
 * @category  Service
 * @package   Mail
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2014 Obullo
 * @license   http://opensource.org/licenses/MIT
 * @link      http://obullo.com/docs/services
 */
Class Rbac implements ServiceInterface
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
        $c['rbac'] = function () use ($c) {
            return new RbacService($c, $c->load('service/provider/db', 'db'), $c['config']->load('rbac'));
        };
    }
}

// END Rbac class

/* End of file Rbac.php */
/* Location: .classes/Service/Rbac.php */