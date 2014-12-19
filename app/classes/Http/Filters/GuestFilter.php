<?php

namespace Http\Filters;

/**
 * Guest auth authority filter
 *
 * @category  Route
 * @package   Filters
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2014 Obullo
 * @license   http://opensource.org/licenses/MIT
 * @link      http://obullo.com/docs/router
 */
Class GuestFilter
{
    /**
     * User service
     * 
     * @var object
     */
    protected $user;

    /**
     * Constructor
     *
     * @param object $c container
     * 
     * @return void
     */
    public function __construct($c)
    {
        $this->user = $c->load('return service/user');
        
        if ($this->user->identity->isGuest()) {

            $c->load('flash/session')->info('Your session has been expired.');
            $c->load('url')->redirect('/');
        }
    }
}

// END GuestFilter class

/* End of file GuestFilter.php */
/* Location: .Http/Filter/GuestFilter.php */