<?php

class Siac extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('api');
        $this->load->helper('html');
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->load->library('ui');

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
    }

    function Index() {
        $ignore_arr = array('Index', '__construct', '__get');
        $methods = array_diff(get_class_methods(get_class($this)), $ignore_arr);
        asort($methods);
        $links = array_map(function($item) {
            return '<a href="' . $this->module_url . strtolower(get_class()) . '/' . strtolower($item) . '">' . $item . '</a>';
        }, $methods);
        $attributes = array('class' => 'api_endpoint');
        echo ul($links, $attributes);
    }
    /**
     * importa/actualiza el modelo en la base de datos
     */ 
    function import(){
        $result = $this->bpm->import(APPPATH.'modules/siac/assets/model/siac.zip', true, 'SIAC');
        var_dump($result);
    }
    /**
     * Pantalla de Seleccion
     */
     function servicios($idwf=null,$idcase=null,$token_id=null){
         $this->load->helper('file');
         $json= read_file(APPPATH.'modules/'.$this->router->fetch_module() .'/assets/json/servicios.json');
         if(!$servicios=json_decode($json,true)){
             show_error('Archivo json mal configurado');
             exit;
         }
         $this->load->module('bpm/engine');
        // -----load bpm
        $mywf = $this->bpm->load($idwf);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $idcase;
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $this->engine->load_data($wf, $idcase);
        $cpData=$this->bpm->bindObjectToArray($this->engine->data);
        $token=$this->bpm->get_token_byid($token_id);
        $cpData['servicios']=$servicios;
        $cpData['idwf']=$idwf;
        $cpData['idcase']=$idcase;
        $cpData['resourceId']=$token['resourceId'];
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'SIAC::Seleccion de Servicio';
        $cpData['css'] = array();
        $cpData['js'] = array(
            $this->module_url . "assets/jscript/main.js" => 'Funciones Principales',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $this->ui->compose('servicios', 'bootstrap.ui.php', $cpData);
     }
     
    /**
     * Carga de Formulario
     */
    function formulario($idwf,$idcase,$token_id){
        $this->load->module('bpm/engine');
        // -----load bpm
        $mywf = $this->bpm->load($idwf);
        $mywf ['data'] ['idwf'] = $idwf;
        $mywf ['data'] ['case'] = $idcase;
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $this->engine->load_data($wf, $idcase);
        $cpData=$this->bpm->bindObjectToArray($this->engine->data);
        $cpData['token_id']=$token_id;
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'SIAC::Formulario Carga';
        $cpData['css'] = array();
        $cpData['js'] = array(
            $this->module_url . "assets/jscript/main.js" => 'Funciones Principales',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $this->ui->compose('formulario', 'bootstrap.ui.php', $cpData);
    }
    
    function guardar_formulario(){
        var_dump($this->input->post());
    }

}