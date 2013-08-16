<?php

    /**
    * Common Functions
    */
    
    /**
     * Fetch language item
     * 
     * @param string $item
     * @return string
     */
    function lang($item = '')
    {
        $locale = Locale::getInstance();
        $item = ($item == '' OR ! isset($locale->language[$item])) ? false : $locale->language[$item];

        return $item;
    }
    
    /**
    * Grab Obullo Super Object
    *
    * @param object $new_istance  
    */
    function getInstance($new_instance = '') 
    { 
        if(is_object($new_instance))  // fixed HMVC object type of integer bug in php 5.1.6
        {
            Controller::_ob_getInstance_($new_instance);
        }

        return  Controller::_ob_getInstance_(); 
    }

    // --------------------------------------------------------------------

    /**
    * Autoload php5 files.

    * @param string $realname
    * @return
    */
    function autoloader($realname)
    {
        if(class_exists($realname))
        {  
            return;
        }
        // echo $realname.'<br>';

        /*
        if($realname == 'Sess\Src\Sess_Cookie')
        {
            // echo $realname;
            // exit;
           // print_r(get_declared_classes()); exit;
        }
        if(in_array((string)$realname, get_declared_classes(), true))
        { 
            return;
        }
        */

        $packages = getConfig('packages');

        //--------------- MODEL LOADER ---------------//
        
        if(strpos($realname, 'Model\\') === 0 || strpos($realname, 'Models\\') === 0) // User model files.
        {            
            $model_parts = explode('\\', $realname);         

            if(strpos($realname, 'Models\\') === 0)
            {
                if($model_parts[1] == 'Schema')
                {
                    $model_path = MODULES .'models'. DS .'schema'. DS .mb_strtolower($model_parts[2], config('charset')). EXT;
                    require($model_path);
                    return;
                }
                
                $model_path = MODULES .'models'. DS .mb_strtolower($model_parts[1], config('charset')). EXT;
            } 
            else // 'Model\\'
            {
                $router = Router::getInstance();
                
                if($model_parts[1] == 'Schema')
                { 
                    $model_path = MODULES .$router->fetchDirectory(). DS .'model'. DS .'schema'. DS .mb_strtolower($model_parts[2], config('charset')). EXT;
                    require($model_path);
                    return;
                }

                $model_path = MODULES .$router->fetchDirectory(). DS .'model'. DS .mb_strtolower($model_parts[1], config('charset')). EXT;
            }
           
            require($model_path);
                            
            return;
        }

        //--------------- OB PACKAGE LOADER ---------------//

        $ob_parts   = explode('\\', $realname);
        $ob_library = strtolower($ob_parts[0]);
        
        if($realname == 'Email\Mail')
        {
            // print_r($ob_parts); exit();
        }

        $src = '';
        if(isset($ob_parts[1]) AND $ob_parts[1] == 'Src')
        {
            $src = 'src'. DS;
        }
        
        if($ob_library == 'ob') { exit($realname); }

        $package_filename = mb_strtolower($ob_library, config('charset'));
        
        if(isset($packages['dependencies'][$package_filename]['component'])) //  check is it Obullo Package ?
        {
            $class = $package_filename;
            if($src != '') // Driver Request.
            {
                $class = mb_strtolower(end($ob_parts));
            }   // a request example new Email\Mail(); $ob_parts[1] = 'Mail';
            elseif($packages['dependencies'][$package_filename]['component'] == 'library' AND isset($ob_parts[1]))
            {
                $class = mb_strtolower($ob_parts[1]);
            }

            require_once(OB_MODULES .$package_filename. DS .'releases'. DS .$packages['dependencies'][$package_filename]['version']. DS .$src.$class. EXT);
            return;
        }
        else 
        {
            if(strpos($realname, '\\') > 0)
            {
                $user_parts = explode('\\', $realname);
                $class_name = mb_strtolower($user_parts[0], config('charset')); // User Classes

                // print_r($user_parts); exit;
                
                require_once(CLASSES .$class_name. DS .$class_name. EXT);
                return;
            }

            $class_name = mb_strtolower($realname, config('charset')); // User Classes
            require_once(CLASSES .$class_name. DS .$class_name. EXT);   
        }
   
    }

    spl_autoload_register('autoloader', true);

    // --------------------------------------------------------------------

    /**
    * Gets a config item
    *
    * @access    public
    * @param     string $config_name file name
    * @version   0.1
    * @version   0.2 added $config_name var
    *            multiple config support
    * @return    mixed
    */
    function config($item, $config_name = 'config')
    {
        static $config_item = array();

        if ( ! isset($config_item[$item]))
        {
            $config_name = getConfig($config_name);

            if ( ! isset($config_name[$item]))
            {
                return false;
            }

            $config_item[$item] = $config_name[$item];
        }

        return $config_item[$item];
    }

    // --------------------------------------------------------------------

    /**
    * Gets a db configuration items
    *
    * @access    public
    * @param     string $item
    * @param     string $index 'default'
    * @version   0.1
    * @version   0.2 added multiple config fetch
    * @return    mixed
    */
    function db($item, $index = 'db')
    {
        static $db_item = array();

        if ( ! isset($db_item[$index][$item]))
        {
            $database = getConfig('database');

            if ( ! isset($database[$index][$item]))
            {
                return false;
            }

            $db_item[$index][$item] = $database[$index][$item];
        }

        return $db_item[$index][$item];
    }

    // --------------------------------------------------------------------

    /**
    *  Check requested obullo package
    *  whether to installed.
    */
    function packageExists($package)
    {
        $packages = getConfig('packages');

        if(isset($packages['dependencies'][$package]['component']))
        {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
    * Set HTTP Status Header
    *
    * @access   public
    * @param    int     the status code
    * @param    string    
    * @return   void
    */
    function setStatusHeader($code = 200, $text = '')
    {
        $stati = array(
                            200    => 'OK',
                            201    => 'Created',
                            202    => 'Accepted',
                            203    => 'Non-Authoritative Information',
                            204    => 'No Content',
                            205    => 'Reset Content',
                            206    => 'Partial Content',

                            300    => 'Multiple Choices',
                            301    => 'Moved Permanently',
                            302    => 'Found',
                            304    => 'Not Modified',
                            305    => 'Use Proxy',
                            307    => 'Temporary Redirect',

                            400    => 'Bad Request',
                            401    => 'Unauthorized',
                            403    => 'Forbidden',
                            404    => 'Not Found',
                            405    => 'Method Not Allowed',
                            406    => 'Not Acceptable',
                            407    => 'Proxy Authentication Required',
                            408    => 'Request Timeout',
                            409    => 'Conflict',
                            410    => 'Gone',
                            411    => 'Length Required',
                            412    => 'Precondition Failed',
                            413    => 'Request Entity Too Large',
                            414    => 'Request-URI Too Long',
                            415    => 'Unsupported Media Type',
                            416    => 'Requested Range Not Satisfiable',
                            417    => 'Expectation Failed',

                            500    => 'Internal Server Error',
                            501    => 'Not Implemented',
                            502    => 'Bad Gateway',
                            503    => 'Service Unavailable',
                            504    => 'Gateway Timeout',
                            505    => 'HTTP Version Not Supported'
                        );

        if ($code == '' OR ! is_numeric($code))
        {
            showError('Status codes must be numeric', 500);
        }

        if (isset($stati[$code]) AND $text == '')
        {                
            $text = $stati[$code];
        }

        if ($text == '')
        {
            showError('No status text available.  Please check your status code number or supply your own message text.', 500);
        }

        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : false;

        if (substr(php_sapi_name(), 0, 3) == 'cgi')
        {
            header("Status: {$code} {$text}", true);
        }
        elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
        {
            header($server_protocol." {$code} {$text}", true, $code);
        }
        else
        {
            header("HTTP/1.1 {$code} {$text}", true, $code);
        }
    }

    //----------------------------------------------------------------------- 

    /**
    * 404 Page Not Found Handler
    *
    * @access   private
    * @param    string
    * @return   string
    */
    function show404($page = '')
    {    
        log\me('error', '404 Page Not Found --> '.$page, false, true);

        echo showHttpError('404 Page Not Found', $page, 'ob_404', 404);

        exit();
    }

    // -------------------------------------------------------------------- 

    /**
    * Manually Set General Http Errors
    * 
    * @param string $message
    * @param int    $status_code
    * @param int    $heading
    * 
    * @version 0.1
    * @version 0.2  added custom $heading params for users
    */
    function showError($message, $status_code = 500, $heading = 'An Error Was Encountered')
    {
        log\me('error', 'HTTP Error --> '.$message, false, true);

        // Some times we use utf8 chars in errors.
        header('Content-type: text/html; charset='.config('charset')); 

        echo showHttpError($heading, $message, 'ob_general', $status_code);

        exit();
    }

                   
    // --------------------------------------------------------------------

    /**
    * General Http Errors
    *
    * @access   private
    * @param    string    the heading
    * @param    string    the message
    * @param    string    the template name
    * @param    int       header status code
    * @return   string
    */
    function showHttpError($heading, $message, $template = 'ob_general', $status_code = 500)
    {
        setStatusHeader($status_code);

        $message = implode('<br />', ( ! is_array($message)) ? array($message) : $message);
        
        if(defined('STDIN'))  // If Command Line Request
        {
            return '['.$heading.']: The url ' .$message. ' you requested was not found.'."\n";
        }
        
        ob_start();
        include(APP .'errors'. DS .$template. EXT);
        $buffer = ob_get_clean();
        
        return $buffer;
    }

    // --------------------------------------------------------------------

    /**
    * Tests for file writability
    *
    * is_writable() returns true on Windows servers when you really can't write to
    * the file, based on the read-only attribute.  is_writable() is also unreliable
    * on Unix servers if safe_mode is on.
    *
    * @access    private
    * @return    void
    */
    function isReallyWritable($file)
    {
        // If we're on a Unix server with safe_mode off we call is_writable
        if (DS == '/' AND @ini_get("safe_mode") == false)
        {
            return is_writable($file);
        }

        // For windows servers and safe_mode "on" installations we'll actually
        // write a file then read it.  Bah...
        if (is_dir($file))
        {
            $file = rtrim($file, DS). DS .md5(rand(1,100));

            if (($fp = @fopen($file, 'ab')) === false)
            {
                return false;
            }

            fclose($fp);
            @chmod($file, '0777');
            @unlink($file);
            return true;
        }
        elseif (($fp = @fopen($file, 'ab')) === false)
        {
            return false;
        }

        fclose($fp);

        return true;
    }
    
    // -------------------------------------------------------------------- 

    /**
    * Remove Invisible Characters
    *
    * This prevents sandwiching null characters
    * between ascii characters, like Java\0script.
    *
    * @access	public
    * @param	string
    * @return	string
    */
    function removeInvisibleCharacters($str, $url_encoded = true)
    {
        $non_displayables = array();

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded)
        {
            $non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

        do
        {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }
        while ($count);

        return $str;
    }

// END common.php File

/* End of file common.php */
/* Location: ./ob/obullo/releases/2.0/src/common.php */