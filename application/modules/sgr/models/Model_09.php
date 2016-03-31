<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_09 extends CI_Model {

    public function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->helper('sgr/tools');

        $this->anexo = '09';
        /* Additional SGR users */
        $this->load->model('sgr/sgr_model');
        $additional_users = $this->sgr_model->additional_users($this->session->userdata('iduser'));
        $this->idu = (isset($additional_users)) ? $additional_users['sgr_idu'] : $this->session->userdata('iduser');
        /* SWITCH TO SGR DB */
        $this->load->library('cimongo/Cimongo.php', '', 'sgr_db');
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

    function save($parameter) {
        $period = $this->session->userdata['period'];
        $container = 'container.sgr_anexo_' . $this->anexo;

        $parameter['period'] = $period;
        $parameter['origen'] = "2013";
        $id = $this->app->genid_sgr($container);

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
        $parameter['idu'] = (float) $this->idu;
        $parameter['origen'] = "2013";

        /*
         * VERIFICO PENDIENTE           
         */
        $get_period = $this->sgr_model->get_current_period_info($this->anexo, $period);

        /* UPDATE */
        if (isset($get_period['status']))
            $this->update_period($get_period['id'], $get_period['status']);

        $result = $this->app->put_array_sgr($id, $container, $parameter);

        if (isset($result)) {
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

        $options = array('upsert' => true, 'w' => 1);
        $container = 'container.sgr_periodos';
        $query = array('id' => (float) $id);
        $parameter = array(
            'status' => 'rectificado',
            'rectified_on' => date('Y-m-d h:i:s'),
            'others' => $this->session->userdata['others'],
            'reason' => $this->session->userdata['rectify']
        );
        $rs = $this->mongowrapper->sgr->$container->update($query, array('$set' => $parameter), $options);
        return $rs['err'];
    }

    function get_anexo_info($anexo, $parameter) {

        /* HEADER TEMPLATE */
        $header_data = array();

        $header = $this->parser->parse('prints/anexo_' . $anexo . '_header', TRUE);
        $tmpl = array('data' => $header);

        $data = array($tmpl);

        $anexoValues = $this->get_anexo_data($anexo, $parameter);
        foreach ($anexoValues as $values) {
            $data[] = array_values($values);
        }
        $this->load->library('table_custom');
        $newTable = $this->table_custom->generate($data);
        return $newTable;
    }

    function get_anexo_data($anexo, $parameter) {
        header('Content-type: text/html; charset=UTF-8');
        $rtn = array();
        $container = 'container.sgr_anexo_' . $anexo;
        $query = array("filename" => $parameter);
        $result = $this->mongowrapper->sgr->$container->find($query);

        foreach ($result as $list) {
            /* Vars 								
             */

            $this->load->model('padfyj_model');
            $transmitter_name = $this->padfyj_model->search_name($list['CUIT_EMISOR']);
            $transmitter_name = ($transmitter_name) ? $transmitter_name : strtoupper($list['EMISOR']);

            $depositories_name = $this->sgr_model->get_depositories($list['CUIT_DEPOSITARIO']);
            $depositories_name = ($depositories_name) ? $depositories_name['nombre'] : strtoupper($list['ENTIDAD_DESPOSITARIA']);

            $this->load->model('app');


            $get_month = explode("-", $list['period']);
            $month_value = translate_month_spanish($get_month[0]);



            $warranty_sum = $this->model_12->get_period_amount($list['period']);


            $col9 = array_sum(array($list['80_HASTA_FEB_2010'], $list['80_DESDE_FEB_2010'], $list['80_DESDE_ENE_2011']));
            $col10 = array_sum(array($list['120_HASTA_FEB_2010'], $list['120_DESDE_FEB_2010'], $list['120_DESDE_ENE_2011']));
            $col13 = $list['FDR_TOTAL_COMPUTABLE'] - $list['FDR_CONTINGENTE'];
            $col14 = $list['GARANTIAS_VIGENTES'] / $list['FDR_TOTAL_COMPUTABLE'];
            $col15 = $col9 / $list['FDR_TOTAL_COMPUTABLE'];
            $col16 = $col10 / $list['FDR_TOTAL_COMPUTABLE'];

            $new_list = array();
            $new_list['col1'] = $month_value;
            $new_list['col2'] = money_format_custom($warranty_sum);
            $new_list['col3'] = money_format_custom($list['80_HASTA_FEB_2010']);
            $new_list['col4'] = money_format_custom($list['120_HASTA_FEB_2010']);
            $new_list['col5'] = money_format_custom($list['80_DESDE_FEB_2010']);
            $new_list['col6'] = money_format_custom($list['120_DESDE_FEB_2010']);
            $new_list['col7'] = money_format_custom($list['80_DESDE_ENE_2011']);
            $new_list['col8'] = money_format_custom($list['120_DESDE_ENE_2011']);
            $new_list['col9'] = money_format_custom($col9, true);
            $new_list['col10'] = money_format_custom($col10, true);
            $new_list['col11'] = money_format_custom($list['FDR_TOTAL_COMPUTABLE']);
            $new_list['col12'] = money_format_custom($list['FDR_CONTINGENTE']);
            $new_list['col13'] = money_format_custom($col13, true);
            $new_list['col14'] = percent_format_custom($col14);
            $new_list['col15'] = percent_format_custom($col15);
            $new_list['col16'] = percent_format_custom($col16);
            $rtn[] = $new_list;
        }
        return $rtn;
    }

}
