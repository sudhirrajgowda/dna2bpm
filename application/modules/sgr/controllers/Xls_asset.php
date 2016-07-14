<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * ASSETS Controller
 * This file allows you to  access assets from within your modules directory
 * 
 * @author Diego Otero
 * 
 * @version 	2.0 (2016-04-06)
 * 
 */

class Xls_asset extends MX_Controller {

    function __construct() {
        parent::__construct();
       
    }
    
    function index(){
        
        //$this->user->authorize();
        //---get working directory and map it to your module
        $count_segments = $this->uri->segments; 
        
        $var = array_shift($this->uri->segments);
        $var = array_shift($this->uri->segments);
        $var = array_shift($this->uri->segments);
        $var = array_shift($this->uri->segments);
        
        
        
         if(count($count_segments)==4){    
              $filename = $var;
              $anexo_num = null;
        } else {
            /*Anexo NUM*/
            $anexo_num = $var ."/";
            $filename = implode('/', $this->uri->segments);
        }
        
         
        
        $file = getcwd() . '/anexos_sgr/'.$anexo_num . $filename;
        $file = str_replace("%20", " ", $file);
        //----get path parts form extension
        $path_parts = pathinfo($file);
        
     
        //---set the type for the headers
        $file_type=  strtolower($path_parts['extension']);        
        if (is_file($file)) {
            header('Content-type: application/vnd.ms-excel'); 
            readfile($file);
              
        } else { 
             show_error("Error getting files: ".str_replace("%20", " ", $filename));
             exit;
           
        }
        exit;
    }

}