<?php
namespace Ob;

Class Welcome extends Controller {
    
    function __construct()
    {           
        parent::__construct();
       
        // new Db\Connect();
        // new Auth\Auth();
        new test\start();
    }
    
    public function index()
    {        
        // new view\start();
        
        // view\set_var('variable', 'and generated by obullo.');

        // $user  = new Model\User();
        // $user  = new Model\Models\User();
        // $email = new Email(false);
        
        // new Auth();
        
        new sess\start();
        
        sess\set();
        
        vi\get('welcome', '', false); // current module view
        // vi\views\get('welcome', '', false); // modules/views
    }
    
    function hmvc()
    {
        new request\start();

        $data = array();
        $data['response_a'] = request\get('welcome/test/1/2/3');
        $data['response_b'] = request\get('welcome/test/4/5/6');
        
        vi\get('hmvc', $data, false);
    }
    
    function test($arg1 = '', $arg2 = '', $arg3 = '')
    {
        echo '<pre>Response: '.$arg1 .' - '.$arg2. ' - '.$arg3.'</pre>';
    }
     
    function task($mode = '')
    {
        if(PHP_OS != 'Linux')
        {
            exit('Please run task functionality under the linux platforms.');
        }
        
        new task\start(); // Call the task helper
        
        echo "<font size='2'>";
        echo "You should run this command with none true or 'false' ";
        echo "argument when you go LIVE server ! .<br />.e.g. task_run('module/controller/method', false);";
        echo '<br />';
        echo url\anchor('welcome/task/help', 'Click Here to Help !');
        echo "<font>";
        
        if($mode == 'help')
        {
            $output = task\run('start/help', $output = true);  // use without true when you go live.
            echo '<pre>'.$output.'</pre>';
        }
        else
        {
            $output = task\run('start/index', $output = true); // use without true when you go live.
            echo '<pre>'.$output.'</pre>';
        }
    }
    
}

/* End of file start.php */
/* Location: .modules/welcome/controllers/welcome.php */