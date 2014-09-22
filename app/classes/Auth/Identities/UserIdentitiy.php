<?php

namespace Auth\Identities;

use Obullo\Auth\Identities\IdentityInterface,
    Auth\Credentials;

/**
 * User Identity
 *
 * @category  Auth
 * @package   Identities
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2014 Obullo
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL Licence
 * @link      http://obullo.com/package/auth
 */
Class User implements IdentityInterface
{
    /**
     * All of the user's attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Create a new generic User object.
     *
     * @param array $attributes user identities
     * 
     * @return void
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->attributes[Credentials::IDENTIFIER];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->attributes[Credentials::PASSWORD];
    }

    /**
     * Get the password needs rehash array.
     *
     * @return mixed false|string new password hash
     */
    public function getPasswordNeedsReHash()
    {
        return isset($this->attributes['__passwordNeedsRehash']['hash']) ? $this->attributes['__passwordNeedsRehash']['hash'] : false;
    }

    /**
     * Get user type : UNVERIFIED, AUTHORIZED, GUEST
     * 
     * @return string
     */
    public function getType()
    {
        return $this->attributes['__type'];
    }

    /**
     * Returns to "1" user if used remember me
     * 
     * @return integer
     */
    public function getRememberMe() 
    {
        return $this->attributes['__rememberMe'];
    }

    /**
     * Get roles of user
     * 
     * @return array
     */
    public function getRoles()
    {
        return isset($this->attributes['__roles']) ? $this->attributes['__roles'] : array();
    }

    /**
     * Dynamically access the user's attributes.
     *
     * @param string $key ket
     * 
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set the user's attributes.
     *
     * @param string $key key
     * @param string $val value
     * 
     * @return mixed
     */
    public function __set($key, $val)
    {
        return $this->attributes[$key] = $val;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param string $key key
     * 
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param string $key key
     * 
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

}

/* End of file UserIdentity.php */
/* Location: .app/classes/Auth/Identities/UserIdentity.php */