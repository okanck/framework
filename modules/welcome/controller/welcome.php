<?php

Class Welcome extends Controller {
    
    function __construct()
    {
        parent::__construct();

        // new Email();
        // new form_Json\start();
        // function hasUpperCaseLetter($string)
        
        // new Acl();
        // new Db();
        // print_r($this->db->get('users')->resultArray());
        
        // var_dump($this->ftp);
        // new sess\start();
    }
    
    public function index()
    {  
        // new Agent\Agent();
        // new view\start(); 
        // vi\setVar('variable', 'and generated by obullo.');

        // new request\start();
        
        // echo request\get('welcome/test/1/2/3');
        // echo request\get('welcome/test/4/5/6');
          
        // $user  = new \Model\User();
        // $user->test();
        // $user  = new Model\User();
        // $email = new Email(false);
        // $this->db->get('users');
        // new Auth();
        
        // new sess\start();
        
        // sess\set('test', 1234);
        // echo sess\get('test');
        vi\view('welcome', '', false); // current module view
        //
        // vi\views('welcome', '', false); // modules/views
    }
    
    function hmvc()
    {
        new request\start();

        $data = array();
        $data['response_a'] = request\get('welcome/test/1/2/3');
        $data['response_b'] = request\get('welcome/test/4/5/6');
        
        vi\view('hmvc', $data, false);
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