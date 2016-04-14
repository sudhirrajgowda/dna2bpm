<?php

class fonapyme_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        $this->load->model('bpm/bpm');
    }

    function get_evaluator_by_project_by_id($query) {

        $rtn = array();
        $container = 'container.proyectos_fonapyme';
        $fields = array('8668', 'id', '8339', '8325', '8340', '8334');
        $query = array(8668 => array('$exists' => true));
        $rs = $this->mongowrapper->db->$container->find($query, $fields);
        foreach ($rs as $list) {
            unset($list['_id']);
            $rtn[] = $list;
        }



        return $rtn;
    }

    function get_company_by_project_by_id($company_id) {

        $rtn = array();
        $container = 'container.empresas';
        $fields = array('8668', 'id', '8339', '8325', '8340', '8334');
        $query = array('id' => $company_id);
        $rs = $this->mongowrapper->db->$container->find($query);
        foreach ($rs as $list) {
            unset($list['_id']);
            $rtn[] = $list;
        }

        return $rtn;
    }

    function get_evaluator_by_project($filter) {
        /* get ids */
        $all_ids = array();
        $arr_status = array();

        $allcases = $this->bpm->get_cases_byFilter($filter, array('id', 'idwf', 'data'));
        foreach ($allcases as $case) {
            if (isset($case['data']['Proyectos_fonapyme']['query']))
                $all_ids[] = $case['data']['Proyectos_fonapyme']['query'];
        }

        $get_value = array_map(function ($all_ids) {
            return $this->get_evaluator_by_project_by_id($all_ids);
        }, $all_ids);

        return $get_value;
    }

    /* MONTOS POR ESTADO */

    function get_amount_stats_by_id($query) {
        $rtn = array();
        $container = 'container.proyectos_fonapyme';
        $fields = array('8334', '8326', '8573');
        $rs = $this->mongowrapper->db->$container->find($query, $fields);
        foreach ($rs as $list) {
            unset($list['_id']);
            $rtn[] = $list;
        }
        return $rtn;
    }

    function delegate_case_action($idwf, $idcase, $iduser_dest) {
        /* UPDATE CASE */
        $collection = 'case';
        $query = array('id' => $idcase, 'idwf' => $idwf);
        $fields = array('iduser');
        $action = array('$set' => array('iduser' => $iduser_dest));
        $options = array('upsert' => true);
        $fnd = $this->mongowrapper->db->$collection->findOne($query, $fields);
        $rs = $this->mongowrapper->db->$collection->update($query, $action, $options);

        if (isset($rs)) {
            $last_user = $fnd['iduser'];
            /* UPDATE TOKENS */
            $collection = 'tokens';
            $query = array('case' => $idcase, 'assign' => $last_user);
            $action = array('$addToSet' => array('assign' => $iduser_dest));
            $options = array('upsert' => true);
            $rf = $this->mongowrapper->db->$collection->update($query, $action, $options);
        }


        exit;
    }

}