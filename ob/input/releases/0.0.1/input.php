<?php
namespace Ob;

/**
 * Input Class
 *
 * @package     Obullo
 * @subpackage  Libraries
 * @category    Language
 * @author      Obullo Team
 * @link        
 */

Class Input {
        
    public static $instance;
    
    public $ip_address            = FALSE;
    public $user_agent            = FALSE;
    public $_allow_get_array      = FALSE;
    public $_standardize_newlines = TRUE;
    public $enable_csrf           = FALSE;
    public $enable_xss            = FALSE;
    
    /**
    * Constructor
    */
    public function __construct()
    { 
        $this->_allow_get_array = (config('enable_query_strings') === TRUE) ? TRUE : FALSE;
        $this->enable_xss       = (config('global_xss_filtering') === TRUE) ? TRUE : FALSE;
        $this->enable_csrf      = (config('csrf_protection') === TRUE) ? TRUE : FALSE;

        log\me('debug', "Input Class Initialized");
    }
    
    // --------------------------------------------------------------------

    public static function getInstance()
    {
       if( ! self::$instance instanceof self)
       {
           self::$instance = new self();
       } 
       
       return self::$instance;
    }
    
    // --------------------------------------------------------------------
    
    /**
    * Sanitize Globals
    * This function does the following:
    * Unsets $_GET data (if query strings are not enabled)
    * Unsets all globals if register_globals is enabled.
    *
    * Standardizes newline characters to \n
    *
    * @access    private
    * @return    void
    */
    public function _sanitize_globals()
    {
        // Would kind of be "wrong" to unset any of these GLOBALS
        $protected = array('_SERVER', '_GET', '_POST', '_FILES', '_REQUEST', '_SESSION', '_ENV', '_controller',
        'GLOBALS', 'HTTP_RAW_POST_DATA');

        // Unset globals for security.
        // This is effectively the same as register_globals = off
        foreach (array($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV, (isset($_SESSION) && is_array($_SESSION)) ? $_SESSION : array()) as $global)
        {
            if ( ! is_array($global))
            {
                if ( ! in_array($global, $protected))
                {
                    unset($GLOBALS[$global]);
                }
            }
            else
            {
                foreach ($global as $key => $val)
                {
                    if ( ! in_array($key, $protected))
                    {
                        unset($GLOBALS[$key]);
                    }

                    if (is_array($val))
                    {
                        foreach($val as $k => $v)
                        {
                            if ( ! in_array($k, $protected))
                            {
                                unset($GLOBALS[$k]);
                            }
                        }
                    }
                }
            }
        }

        // Is $_GET data allowed? If not we'll set the $_GET to an empty array
        if ($this->_allow_get_array == FALSE)
        {
            $_GET = array();
        }
        else
        {
            $_GET = $this->_clean_input_data($_GET);
        }

        // Clean $_POST Data
        $_POST = $this->_clean_input_data($_POST);

        // Sanitize PHP_SELF
        $_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);

        // CSRF Protection check
        if ($this->enable_csrf == TRUE)
        {
            Security::getInstance()->csrf_verify();
        }
        
        // Clean $_COOKIE Data
        // Also get rid of specially treated cookies that might be set by a server
        // or silly application, that are of no use to a OB application anyway
        // but that when present will trip our 'Disallowed Key Characters' alarm
        // http://www.ietf.org/rfc/rfc2109.txt
        // note that the key names below are single quoted strings, and are not PHP variables
        unset($_COOKIE['$Version']);
        unset($_COOKIE['$Path']);
        unset($_COOKIE['$Domain']);
        
        $_COOKIE = $this->_clean_input_data($_COOKIE);

        log\me('debug', "Global POST and COOKIE data sanitized");
    }
    
    // ------------------------------------------------------------------------
    
    /**
    * Fetch the IP Address
    *
    * @access    public
    * @return    string
    */
    public function ip_address()
    {
        if ($this->ip_address !== FALSE)
        {
            return $this->ip_address;
        }

        if (config('proxy_ips') != '' && $_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['REMOTE_ADDR'])
        {
            $proxies = preg_split('/[\s,]/', config('proxy_ips'), -1, PREG_SPLIT_NO_EMPTY);
            $proxies = is_array($proxies) ? $proxies : array($proxies);

            $this->ip_address = in_array($_SERVER['REMOTE_ADDR'], $proxies) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        }
        elseif ($_SERVER['REMOTE_ADDR'] AND $_SERVER['HTTP_CLIENT_IP'])
        {
            $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif ($_SERVER['REMOTE_ADDR'])
        {
            $this->ip_address = $_SERVER['REMOTE_ADDR'];
        }
        elseif ($_SERVER['HTTP_CLIENT_IP'])
        {
            $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif ($_SERVER['HTTP_X_FORWARDED_FOR'])
        {
            $this->ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if ($this->ip_address === FALSE)
        {
            $this->ip_address = '0.0.0.0';
            
            return $this->ip_address;
        }

        if (strstr($this->ip_address, ','))
        {
            $x = explode(',', $this->ip_address);
            
            $this->ip_address = trim(end($x));
        }

        if ( ! i_valid_ip($this->ip_address))
        {
            $this->ip_address = '0.0.0.0';
        }

        return $this->ip_address;
    }
    
    // ------------------------------------------------------------------------
    
    /**
    * Validate IP Address
    *
    * Updated version suggested by Geert De Deckere
    *
    * @access   public
    * @param    string
    * @return   string
    */
    public function valid_ip($ip)
    {
        $ip_segments = explode('.', $ip);

        // Always 4 segments needed
        if (count($ip_segments) != 4)
        {
            return FALSE;
        }
        // IP can not start with 0
        if ($ip_segments[0][0] == '0')
        {
            return FALSE;
        }
        // Check each segment
        foreach ($ip_segments as $segment)
        {
            // IP segments must be digits and can not be
            // longer than 3 digits or greater then 255
            if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
            {
                return FALSE;
            }
        }

        return TRUE;
    }
   
    // ------------------------------------------------------------------------

    /**
    * Clean Input Data
    *
    * This is a helper function. It escapes data and
    * standardizes newline characters to \n
    *
    * @access   private
    * @param    string
    * @return   string
    */
    public function _clean_input_data($str)
    {
        if (is_array($str))
        {
            $new_array = array();
            foreach ($str as $key => $val)
            {
                $new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
            }
            
            return $new_array;
        }

        // We strip slashes if magic quotes is on to keep things consistent
        if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }  

        // Remove control characters
	$str = remove_invisible_characters($str);
        
        // Should we filter the input data?
        if ($this->enable_xss === TRUE)
        {
            $str = Security::getInstance()->xss_clean($str);
        }

        // Standardize newlines if needed
        if ($this->_standardize_newlines == TRUE)
        {
            if (strpos($str, "\r") !== FALSE)
            {
                $str = str_replace(array("\r\n", "\r", "\r\n\n"), PHP_EOL, $str);
            }
        }
        
        return $str;
    }
    
    // ------------------------------------------------------------------------
    
    /**
    * Clean Keys
    *
    * This is a helper function. To prevent malicious users
    * from trying to exploit keys we make sure that keys are
    * only named with alpha-numeric text and a few other items.
    *
    * @access   private
    * @param    string
    * @return   string
    */
    public function _clean_input_keys($str)
    {
        if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
        {
            exit('Disallowed Key Characters.');
        }

        return $str;
    }
    
    // --------------------------------------------------------------------

    /**
    * Fetch from array
    *
    * This is a helper function to retrieve values from global arrays
    *
    * @access   public
    * @param    array
    * @param    string
    * @param    bool
    * @return   string
    */
    public function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE)
    {
        if ( ! isset($array[$index]))
        {
            return FALSE;
        }

        if ($xss_clean === TRUE)
        {
            return Security::getInstance()->xss_clean($array[$index]);
        }

        return $array[$index];
    }
   
}

// END Input Class
// -------------------------------------------------------------------- 

/* End of file input.php */
/* Location: ./ob/input/releases/0.0.1/input.php */