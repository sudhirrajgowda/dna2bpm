<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_123 extends CI_Model {

    public function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->helper('sgr/tools');

        $this->anexo = '123';
        $this->idu = (int) $this->session->userdata('iduser');
        /*SWITCH TO SGR DB*/
        $this->load->library('cimongo/cimongo','','sgr_db');
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
         * @example .... NRO_GARANTIA	NUMERO_CUOTA_CUYO_VENC_MODIFICA	FECHA_VENC_CUOTA	FECHA_VENC_CUOTA_NUEVA	MONTO_CUOTA	SALDO_AL_VENCIMIENTO


         * */
        $defdna = array(
            1 => 'NRO_GARANTIA', //NRO_GARANTIA
            2 => 'NUMERO_CUOTA_CUYO_VENC_MODIFICA', //NUMERO_CUOTA_CUYO_VENC_MODIFICA
            3 => 'FECHA_VENC_CUOTA', //FECHA_VENC_CUOTA
            4 => 'FECHA_VENC_CUOTA_NUEVA', //FECHA_VENC_CUOTA_NUEVA
            5 => 'MONTO_CUOTA', //MONTO_CUOTA
            6 => 'SALDO_AL_VENCIMIENTO', //SALDO_AL_VENCIMIENTO
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
        $parameter['FECHA_VENC_CUOTA'] = strftime("%Y-%m-%d", mktime(0, 0, 0, 1, -1 + $parameter['FECHA_VENC_CUOTA'], 1900));
        $parameter['FECHA_VENC_CUOTA_NUEVA'] = strftime("%Y-%m-%d", mktime(0, 0, 0, 1, -1 + $parameter['FECHA_VENC_CUOTA_NUEVA'], 1900));

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

        $headerArr = array("NRO_ORDEN", "DIA1", "DIA2", "DIA3", "DIA4", "DIA5", "DIA6", "DIA7", "DIA8", "DIA9", "DIA10", "DIA11", "DIA12", "DIA13", "DIA14", "DIA15", "DIA16", "DIA17", "DIA18", "DIA19", "DIA20", "DIA21", "DIA22", "DIA23", "DIA24", "DIA25", "DIA26", "DIA27", "DIA28", "DIA29", "DIA30", "DIA31", "PROMEDIO");
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
            $new_list = array();
            $new_list['NRO_GARANTIA'] = $list['NRO_GARANTIA'];
            $new_list['NUMERO_CUOTA_CUYO_VENC_MODIFICA'] = $list['NUMERO_CUOTA_CUYO_VENC_MODIFICA'];
            $new_list['FECHA_VENC_CUOTA'] = $list['FECHA_VENC_CUOTA'];
            $new_list['FECHA_VENC_CUOTA_NUEVA'] = $list['FECHA_VENC_CUOTA_NUEVA'];
            $new_list['MONTO_CUOTA'] = money_format_custom($list['MONTO_CUOTA']);
            $new_list['SALDO_AL_VENCIMIENTO'] = money_format_custom($list['SALDO_AL_VENCIMIENTO']);
            $rtn[] = $new_list;
        }
        return $rtn;
    }

}
