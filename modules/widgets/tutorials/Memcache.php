<?php

namespace Widgets\Tutorials;

Class Memcache extends \Controller
{
    /**
     * Loader
     * 
     * @return void
     */
    public function load()
    {
        $this->c['view'];
        $this->cache = $this->c['service provider cache']->get(
            [
                'driver' => 'memcached', 'serializer' => 'SERIALIZER_PHP'
            ]
        );
    }

    /**
     * Index
     * 
     * @return void
     */
    public function index()
    {
        // $this->cache->set('test:value', array('a' => array('b', 'c', 'd')));

        // $this->cache->delete('Auth:__permanent:Authorized:user@example.com');
        // print_r($this->cache->get('Auth:__permanent:Authorized:user@example.com:cnoom8p14x'));

        $allkeys = $this->cache->getAllKeys();

        foreach ($allkeys as $key => $value) {
            echo $key.':'.$value.'<br />';
        }

        // print_r($allkeys);

    }
}