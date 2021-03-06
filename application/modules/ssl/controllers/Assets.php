<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * ASSETS Controller
 * 
 * This file allows you to  access assets from within your modules directory
 * 
 * @author Borda Juan Ignacio
 * 
 * @version 	1.0 (2012-05-27)
 * @ignore 
 */

class assets extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->user->authorize();
        //---get working directory and map it to your module
        $file = getcwd() . '/application/modules/' . implode('/', $this->uri->segments);
        //----get path parts form extension
        $path_parts = pathinfo( $file);
        //---set the type for the headers
        $file_type=  strtolower($path_parts['extension']);
        
        if (is_file($file)) {
            //----write propper headers
            switch ($file_type) {
                case 'css':
                    header('Content-type: text/css');
                    break;

                case 'js':
                    header('Content-type: text/javascript');
                    break;
                
                case 'json':
                    header('Content-type: application/json;charset=UTF-8');
                    break;
                
                case 'xml':
                   header('Content-type: text/xml');
                    break;
                
                case 'pdf':
                  header('Content-type: application/pdf');
                    break;
                
                case 'jpg' || 'jpeg' || 'png' || 'gif':
                    header('Content-type: image/'.$file_type);
                    break;
            }
 
            readfile($file);
        } else {
            show_404();
        }
        exit;
    }

    function code_block($code, $lang='php', $theme='monokai', $rows=16){
    return '<textarea rows="'.$rows.'" class="code_block" theme="'.$theme.'" lang="'.$lang.'">'.$code.'</textarea>';
    }


}