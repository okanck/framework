<?php
 
 /**
 * Obullo Core
 * Version 2.0
 * 
 * @package         Obullo 
 * @subpackage      Obullo.core
 * @category        Core
 * @version         2.0
 */

Class Obullo
{
    /**
     * Run the application
     */
    public function run()
    {   
        $packages = getConfig('packages');
        
        require (APP  .'config'. DS .'constants'. EXT);  // app constants.
        require (OB_MODULES .'obullo'. DS .'releases'. DS .$packages['version']. DS .'src'. DS .'ob'. EXT);
        
        if(packageExists('log')) // check log package is installed.
        {
            new log\start();
        }
               
        if(packageExists('error')) // check error package is installed.
        {
            new error\start();
        }
        
        $uri    = Uri::getInstance(); 
        $router = Router::getInstance();

        new bench\start();
        
        Locale::getInstance();
        
        bench\mark('total_execution_time_start');
        bench\mark('loading_time_base_classes_start');
        
        $input = Input::getInstance();
        $input->_sanitizeGlobals();  // Initalize to input filter. ( Sanitize must be above the GLOBALS !! )             

        $output = Output::getInstance();
        $config = Config::getInstance(); 

        if ($output->_displayCache($config, $uri) == true) { exit; }  // Check REQUEST uri if there is a Cached file exist 

        $folder = 'controller';

        if(defined('STDIN'))  // Command Line Request
        {                
            if($router->fetchDirectory() != 'tasks')    // Check module and application folders.
            {                    
                if(is_dir(MODULES .$router->fetchDirectory(). DS .'tasks'))
                {
                    $folder = 'tasks';
                } 
            }
        }

        $page_uri   = "{$router->fetchDirectory()} / {$router->fetchClass()} / {$router->fetchMethod()}";
        $controller = MODULES .$router->fetchDirectory(). DS .$folder. DS .$router->fetchClass(). EXT;

        bench\mark('loading_time_base_classes_end');  // Set a mark point for benchmarking  
        bench\mark('execution_time_( '.$page_uri.' )_start');  // Mark a start point so we can benchmark the controller 
        
        require ($controller);  // call the controller.

        if ( ! class_exists($router->fetchClass()) OR $router->fetchMethod() == 'controller' 
              OR $router->fetchMethod() == '_output'       // security fix.
              OR $router->fetchMethod() == '_ob_getInstance_'
              OR in_array(strtolower($router->fetchMethod()), array_map('strtolower', get_class_methods('Controller')))
            )
        {
            show404($page_uri);
        }
        
        $Class = $router->fetchClass();
        $OB = new $Class();           // If Everyting ok Declare Called Controller ! 

        if ( ! in_array(strtolower($router->fetchMethod()), array_map('strtolower', get_class_methods($OB))))  // Check method exist or not 
        {
            show404($page_uri);
        }

        $arguments = array_slice($OB->uri->rsegments, 3);

        //                                                                     0       1       2
        // Call the requested method. Any URI segments present (besides the directory/class/method) 
        // will be passed to the method for convenience
        call_user_func_array(array($OB, $router->fetchMethod()), $arguments);

        bench\mark('execution_time_( '.$page_uri.' )_end');  // Mark a benchmark end point 

        // Write Cache file if cache on ! and Send the final rendered output to the browser
        $output->_display();

        while (ob_get_level() > 0) // close all buffers.  
        {
            ob_end_flush();
        }
        
        // Close the Db connection.
        ##############

        $driver = db('dbdriver');
        
        if($driver == 'mongodb' AND isset(getInstance()->db->connection) AND is_object(getInstance()->db->connection))
        {
            getInstance()->db->connection->close();
        } 
        else
        {
            if(isset(getInstance()->db))
            {
                getInstance()->db = null;
            }
        }
    }
    
}

// END obullo.php File

/* End of file obullo.php
/* Location: ./ob/obullo/releases/2.0/obullo.php */