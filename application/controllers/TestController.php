<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestController extends CI_Controller{
    function getData(){
        // echo"hello";
        $array = array(
            'color' => 'red',
            'shape' => 'round',
            'size'  => ''
    );
    
    echo element('color', $array);
    }
}

?>