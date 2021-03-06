<?php

return  array(

    /*
    | -------------------------------------------------------------------
    | Flash
    | -------------------------------------------------------------------
    | This file contains your arrays of flash messages configuration. It is used by the
    | Flash Class to help set notice templates. The array keys are used to identify notices 
    | that is defined in your constants file.
    |
    */
    NOTICE_MESSAGE => '<div class="{class}">{icon}{message}</div>',
    NOTICE_ERROR   => array('class' => 'alert alert-danger', 'icon' => '<span class="glyphicon glyphicon-remove-sign"></span> '),
    NOTICE_SUCCESS => array('class' => 'alert alert-success', 'icon' => '<span class="glyphicon glyphicon-ok-sign"></span> '),
    NOTICE_WARNING => array('class' => 'alert alert-warning', 'icon' => '<span class="glyphicon glyphicon-exclamation-sign"></span> '),
    NOTICE_INFO    => array('class' => 'alert alert-info', 'icon' => '<span class="glyphicon glyphicon-info-sign"></span> '),
);

/* End of file flash.php */
/* Location: .app/config/flash.php */