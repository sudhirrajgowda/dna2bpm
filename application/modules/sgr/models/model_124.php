<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_124 extends CI_Model {

    public function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->helper('sgr/tools');

        $this->anexo = '124';
        $this->idu = (int) $this->session->userdata('iduser');
        /* SWITCH TO SGR DB */
        $this->load->library('cimongo/cimongo', '', 'sgr_db');
        $this->sgr_db->switch_db('sgr');

        if (!$this->idu) {
            header("$this->module_url/user/logout");
        }

        /* DATOS SGR */
        $sgrArr = $this->sgr_model->get_sgr();
        foreach ($sgrArr as $sgr) {
            $this->sgr_id = $sgr['id'];
            $this->sgr_nombre = $sgr['1693'];
        }
    }

    function check($parameter) {
        /**
         *   Funcion ...
         * 
         * @param 
         * @type PHP
         * @name ...
         * @author Diego
         *
         * @example .... NRO_GARANTIA	FECHA_REAFIANZA	SALDO_VIGENTE	REAFIANZADO	RAZON_SOCIAL	CUIT
         * */
        $defdna = array(
            1 => 'NRO_GARANTIA', //NRO_GARANTIA
            2 => 'FECHA_REAFIANZA', //FECHA_REAFIANZA
            3 => 'SALDO_VIGENTE', //SALDO_VIGENTE
            4 => 'REAFIANZADO', //REAFIANZADO
            5 => 'RAZON_SOCIAL', //RAZON_SOCIAL
            6 => 'CUIT', //CUIT
        );


        $insertarr = array();
        foreach ($defdna as $key => $value) {
            $insertarr[$value] = $parameter[$key];
        }
        return $insertarr;
    }

    function save($parameter) {
        $period = $this->session->userdata['period'];
        $container = 'container.sgr_anexo_' . $this->anexo;
        
        /*FILTER NUMBERS/STRINGS*/
        $int_values = array_filter($parameter, 'is_int');
        $float_values = array_filter($parameter, 'is_float');        
        $numbers_values = array_merge($int_values,$float_values);              
        
        /*FIX INFORMATION*/
        $parameter = array_map('trim', $parameter);
        $parameter = array_map('addSlashes', $parameter);

        /* FIX DATE */
        $parameter['FECHA_REAFIANZA'] = strftime("%Y-%m-%d", mktime(0, 0, 0, 1, -1 + $parameter['FECHA_REAFIANZA'], 1900));
        $parameter['period'] = $period;
        $parameter['origin'] = 2013;
        
        $id = $this->app->genid_sgr($container);
        
        /*MERGE CAST*/
        $parameter = array_merge($parameter,$numbers_values);
        $result = $this->app->put_array_sgr($id, $container, $parameter);

        if ($result) {
            $out = array('status' => 'ok');
        } else {
            $out = array('status' => 'error');
        }
        return $out;
    }

    function save_period($parameter) {
        /* ADD PERIOD */
        $container = 'container.sgr_periodos';
        $period = $this->session->userdata['period'];
        $id = $this->app->genid_sgr($container);
        $parameter['period'] = $period;
        $parameter['period_date'] = translate_period_date($period);
        $parameter['status'] = 'activo';
        $parameter['idu'] = $this->idu;

        /*
         * VERIFICO PENDIENTE           
         */
        $get_period = $this->sgr_model->get_period_info($this->anexo, $this->sgr_id, $period);
        $this->update_period($get_period['id'], $get_period['status']);

        $result = $this->app->put_array_sgr($id, $container, $parameter);

        if ($result) {
            /* BORRO SESSION RECTIFY */
            $this->session->unset_userdata('rectify');
            $this->session->unset_userdata('others');
            $this->session->unset_userdata('period');
            $out = array('status' => 'ok');
        } else {
            $out = array('status' => 'error');
        }
        return $out;
    }

     function update_period($id, $status) {
        $options = array('upsert' => true, 'safe' => true);
        $container = 'container.sgr_periodos';
        $query = array('id' => (integer) $id);
        $parameter = array(
            'status' => 'rectificado',
            'rectified_on' => date('Y-m-d h:i:s'),
            'others' => $this->session->userdata['others'],
            'reason' => $this->session->userdata['rectify']
        );
        $rs = $this->mongo->sgr->$container->update($query, array('$set' => $parameter), $options);
        return $rs['err'];
    }

    function get_anexo_info($anexo, $parameter) {

        $headerArr = array("NRO GARANTIA", "FECHA REAFIANZA", "SALDO VIGENTE", "REAFIANZADO", "RAZON SOCIAL", "CUIT");
        $data = array($headerArr);
        $anexoValues = $this->get_anexo_data($anexo, $parameter);
        foreach ($anexoValues as $values) {
            $data[] = array_values($values);
        }
        $this->load->library('table');
        return $this->table->generate($data);
    }

    function get_anexo_data($anexo, $parameter) {
        header('Content-type: text/html; charset=UTF-8');
        $rtn = array();
        $container = 'container.sgr_anexo_' . $anexo;
        $query = array("filename" => $parameter);
        $result = $this->mongo->sgr->$container->find($query);

        foreach ($result as $list) {
            /* Vars */
            $cuit = str_replace("-", "", $list['CUIT']);
            $this->load->model('padfyj_model');
            $brand_name = $this->padfyj_model->search_name($cuit);
            $brand_name = ($brand_name) ? $brand_name : strtoupper($list['RAZON_SOCIAL']);


            $new_list = array();
            $new_list['NRO_GARANTIA'] = $list['NRO_GARANTIA'];
            $new_list['FECHA_REAFIANZA'] = $list['FECHA_REAFIANZA'];
            $new_list['SALDO_VIGENTE'] = money_format_custom($list['SALDO_VIGENTE']);
            $new_list['REAFIANZADO'] = $list['REAFIANZADO'] . "%";
            $new_list['RAZON_SOCIAL'] = $brand_name;
            $new_list['CUIT'] = $list['CUIT'];
            $rtn[] = $new_list;
        }
        return $rtn;
    }

}
