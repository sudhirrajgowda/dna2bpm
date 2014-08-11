<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mysql_model_201 extends CI_Model {

    function mysql_model_201() {
        parent::__construct();
        // IDU : Chequeo de sesion
        $this->idu = (float) switch_users($this->session->userdata('iduser'));
        if (!$this->idu) {
            header("$this->module_url/user/logout");
            exit();
        }

        /* DATOS SGR */
        $sgrArr = $this->sgr_model->get_sgr();
        foreach ($sgrArr as $sgr) {
            $this->sgr_id = $sgr['id'];
            $this->sgr_nombre = $sgr['1693'];
            $this->sgr_cuit = $sgr['1695'];
        }

        $dbconnect = $this->load->database('dna2');
    }

    /* ACTIVE PERIODS DNA2 */

    function active_periods_dna2($anexo, $period) {       

        /* TRANSLATE ANEXO NAME */
        $anexo_dna2 = translate_anexos_dna2($anexo);
        $this->db->where('estado', 'activo');
        $this->db->where('archivo !=', 'Sin Movimiento');
        $this->db->where('periodo NOT LIKE', '%2014'); 
        $this->db->where('anexo', $anexo_dna2);
        $query = $this->db->get('forms2.sgr_control_periodos');

        foreach ($query->result() as $row) {
            $already_period = $this->already_period($row->archivo);
            if (!$already_period) {

                $parameter = array();

                $parameter['anexo'] = translate_anexos_dna2($row->anexo);
                $parameter['filename'] = $row->archivo;
                $parameter['period_date'] = translate_dna2_period_date($row->periodo);
                $parameter['sgr_id'] = (float) $row->sgr_id;
                $parameter['status'] = 'activo';
                $parameter['origen'] = 'forms2';
                $parameter['period'] = str_replace("_", "-", $row->periodo);


                /* UPDATE CTRL PERIOD */
                $this->save_tmp($parameter);

                /* UPDATE ANEXO */
                if ($row->archivo) {
                    $already_update = $this->already_updated($row->anexo, $nro_orden, $filename);
                    if (!$already_update)
                        $this->anexo_data_tmp($anexo_dna2, $row->archivo);
                }
            }
        }
    }
    
    function active_periods_dna2_ori($anexo, $period) {



        /* TRANSLATE ANEXO NAME */
        $anexo_dna2 = translate_anexos_dna2($anexo);
        $this->db->where('estado', 'activo');
        $this->db->where('archivo !=', 'Sin Movimiento');
        $this->db->where('anexo', $anexo_dna2);
        $query = $this->db->get('forms2.sgr_control_periodos');

        foreach ($query->result() as $row) {



            $already_period = $this->already_period($row->archivo);
            if (!$already_period) {

                $parameter = array();

                $parameter['anexo'] = translate_anexos_dna2($row->anexo);
                $parameter['filename'] = $row->archivo;
                $parameter['period_date'] = translate_dna2_period_date($row->periodo);
                $parameter['sgr_id'] = (float) $row->sgr_id;
                $parameter['status'] = 'activo';
                $parameter['origen'] = 'forms2';
                $parameter['period'] = str_replace("_", "-", $row->periodo);


                $is_2014 = explode("_", $row->periodo);
                if ($is_2014[1]!="2014") {
                    
                    /* UPDATE CTRL PERIOD */
                    $this->save_tmp($parameter);

                    /* UPDATE ANEXO */
                    if ($row->archivo) {
                        $already_update = $this->already_updated($row->anexo, $nro_orden, $filename);
                        if (!$already_update)
                            $this->anexo_data_tmp($anexo_dna2, $row->archivo);
                    }
                }
            }
        }
    }
    
        /* UPDATE SIN MOVIMIENTO */

    function active_periods_sm_dna2($anexo, $period) {
        /* TRANSLATE ANEXO NAME */
        $anexo_dna2 = translate_anexos_dna2($anexo);
        $this->db->where('estado', 'activo');
        $this->db->where('archivo', 'Sin Movimiento');
        $this->db->where('anexo', $anexo_dna2);
        $query = $this->db->get('forms2.sgr_control_periodos');

        foreach ($query->result() as $row) {
            $already_period = $this->already_period($row->archivo);
            $parameter = array();

            $parameter['anexo'] = translate_anexos_dna2($row->anexo);
            $parameter['filename'] = $row->archivo;
            $parameter['period_date'] = translate_dna2_period_date($row->periodo);
            $parameter['sgr_id'] = (float) $row->sgr_id;
            $parameter['status'] = 'activo';
            $parameter['origen'] = 'forms2';
            $parameter['period'] = str_replace("_", "-", $row->periodo);


            $is_2014 = explode("_", $row->periodo);
            if ($is_2014[1] != "2014") {
                /* UPDATE CTRL PERIOD */
                $this->save_tmp($parameter);
            }
        }
    }

    function save_tmp($parameter) {


        $parameter = (array) $parameter;
        $container = 'container.sgr_periodos';

        $id = $this->app->genid_sgr($container);
        $result = $this->app->put_array_sgr($id, $container, $parameter);
        if ($result) {
            $out = array('status' => 'ok');
        } else {
            $out = array('status' => 'error');
        }
        return $out;
    }

    /* SAVE FETCHS ANEXO  DATA */

    function anexo_data_tmp($anexo, $filename) {




        $this->db->select(
                'sgr_fdr_integrado.ID as get_ID,
                nro_control,
                cuit_protector,
                fecha_movimiento,
                aporte,
                retiro,
                retiro_de_rendimientos,
                retencion_por_contingente,               
                filename,
                idu'
        );

        if ($filename != 'Sin Movimiento')
            $this->db->where('filename', $filename);


        $this->db->join('sgr_fdr_integrado_numeracion', 'sgr_fdr_integrado_numeracion.ID = sgr_fdr_integrado.ID');
        $query = $this->db->get($anexo);
        $parameter = array();
        foreach ($query->result() as $row) {

            $parameter = array();
            

            /* STRING */

            $parameter["CUIT_PROTECTOR"] = (string) str_replace("-", "", $row->cuit_protector);

            /* INTEGERS  & FLOATS */
            $parameter["APORTE"] = (float) $row->aporte;
            $parameter["RETIRO"] = (float) $row->retiro;
            $parameter["RETENCION_POR_CONTINGENTE"] = (float) $row->retencion_por_contingente;
            $parameter["RETIRO_DE_RENDIMIENTOS"] = (float) $row->retiro_de_rendimientos;

            $parameter["NRO_ACTA"] = (int) $row->NRO_ACTA;
            $parameter["NUMERO_DE_APORTE"] = (int) $row->nro_control;


            $parameter['FECHA_MOVIMIENTO'] = translate_mysql_date($row->fecha_movimiento);

            $parameter['idu'] = (float) $row->idu;
            $parameter['filename'] = (string) $row->filename;
            $parameter['id'] = (float) $row->get_ID;
            $parameter['origen'] = 'forms2';

            debug($parameter);
            
            $this->save_anexo_201_tmp($parameter, $anexo);
        }
    }

    /* SAVE FETCHS ANEXO 201 DATA */

    function already_period($filename) {

        $container = 'container.sgr_periodos';
        $query = array("filename" => $filename);
        $result = $this->mongo->sgr->$container->findOne($query);
        if ($result)
            return true;
    }

    function already_updated($anexo, $nro_orden, $filename) {

        $container = 'container.sgr_anexo_' . $anexo;
        $query = array("filename" => $filename);
        $result = $this->mongo->sgr->$container->findOne($query);

        if ($result)
            return true;
    }
    
    function already_id($anexo, $idvalue) {
        $idvalue = (float) $idvalue;

        $container = 'container.sgr_anexo_' . $anexo;
        $query = array("id" => $idvalue);
        $result = $this->mongo->sgr->$container->findOne($query);
        if ($result)
            return true;
    }

    function save_anexo_201_tmp($parameter, $anexo) {
        $parameter = (array) $parameter;
        $token = $this->idu;
        $period = $this->session->userdata['period'];
        $container = 'container.sgr_anexo_201';
        /* TRANSLATE ANEXO NAME */
        $already_id = $this->already_id($anexo, $parameter['id']);

        if ($already_id) {
            //echo "duplicado" . $parameter['id'];
        } else {

            $id = $this->app->genid_sgr($container);
            $result = $this->app->put_array_sgr($id, $container, $parameter);
            if ($result) {
                $out = array('status' => 'ok');
            } else {
                $out = array('status' => 'error');
            }
        }
        return $out;
    }

}