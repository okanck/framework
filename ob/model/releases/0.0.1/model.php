<?php
namespace Ob;

/**
 * Model Class.
 *
 * Main model class.
 *
 * @package         Obullo 
 * @subpackage      Obullo.core     
 * @category        Core Model
 * @version         0.1
 */                    

Class Model {

    /**
     * Model db loader
     * Database Connection Switch.
     * 
     * @param mixed $db 
     */
    public function __construct($db = true)
    {
        log\me('debug', "Model Class Initialized");
        
        $db_var = 'db';
        if(is_string($db))
        {
            $db_var = $db;
        }
        
        $assign_db = false;
        if(is_bool($db) AND $db == true)
        {
            $assign_db = true;
        }
        
        if($assign_db)
        {
            if( ! isset(getInstance()->{$db_var})) // If database connection not available.
            {
                $database = new Db\Db(); // Create new Database Instance.
                $this->{$db_var} = $database->connect($db_var);
            }
            
            if(is_object(getInstance()->{$db_var}))
            {
                if(method_exists($this, '__get') OR method_exists($this, '__set'))
                {
                    $this->{$db_var} = getInstance()->{$db_var}; // to prevent some reference errors
                }
                else
                {
                    $this->{$db_var} = &getInstance()->{$db_var}; // to prevent some reference errors
                }
            }
        }
    } 
}

// END Model Class

/* End of file model.php */
/* Location: ./ob/model/releases/0.0.1/model.php */