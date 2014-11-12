<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * WEBSERVICE
 * 
 * Description of the class WEBSERVICE
 * 
 * @author Diego Otero <xxcynicxx@gmail.com>
 * @date   Oct 21, 2014
 */
class Webservice extends MX_Controller {

    function __construct() {
        $dbconnect = $this->load->database('dna2');
        $this->load->model('app');
        ini_set("error_reporting", E_ALL);
    }

    public function msg($program = null, $parameter = null) {


        $classes = array('crefis', 'crefis_ucap', 'sgr', 'ksemilla', 'pacc1', 'pacc1_dircon');
        $programas = array('creFis', 'CreFis_UCAP', 'SGR', 'KSEMILLA', 'PACC1', 'PACC1_DIRCON');

        /* PROGRAM CLASSES */
        foreach ($classes as $class)
            $this->load->library("programs/" . $class);



        $get_cuit = array('20-29592934-1', '30-70366211-7', '30-71025327-3');
        $msg = null;
        $show_msg = null;


        $arr_parameter = array($parameter);

        if ($get_cuit) {
            $empresas = (!isset($parameter)) ? $get_cuit : $arr_parameter;
        }

        foreach ($empresas as $CUIT) {

            $cuit = explode('-', $CUIT);

            $id = -1;
            $msg = '<span class="error">No encontrado</span>';
            $tbl_dest = 'td_empresas';
            $this->db->select('*');
            $this->db->where('idpreg', 1695);
            $this->db->like('valor', trim($cuit[1]));
            $query = $this->db->get('td_empresas');
            foreach ($query->result() as $row_cuit) {

                $id = $row_cuit->id;
            }





            foreach ($programas as $nombre) {
                $id_proyectos = array();
                $programa = new $nombre();


                if ($programa->self) {
                    $id_proyectos = $this->search4relSelf($id, $programa->where, $programa->tabladest);
                } else {
                    $id_proyectos = $this->search4rel($id, $programa->where, $programa->tabladest);
                }



                foreach ($id_proyectos as $idrel) {
                    if ($programa->estado != 0) {
                        $ip = ($programa->id != 0) ? $this->test_getvalue($idrel, $programa->id, $programa->tabladest) : "N/A";
                        $get_estado = ($programa->id != 0) ? $this->test_getvalue($idrel, $programa->estado, $programa->tabladest) : null;
                        $titulo = utf8_decode($this->test_getvalue($idrel, $programa->titulo, $programa->tabladest));
                        $monto = ($programa->monto() != 0) ? $this->test_getvalue($idrel, $programa->monto(), $programa->tabladest) : null;

                        $fecha = NULL;
                        $estados = ($programa->estado != 0) ? $this->test_gethist($idrel, $programa->estado, $programa->tabladest) : array();



                        if (is_array($estados)) {
                            asort($estados);
                            foreach ($estados as $estado => $fecha) {
                                $fecha = date('d/m/Y', strtotime($fecha));
                            }
                        }


                        if (isset($get_estado)) {
                            $idopcion = $this->get_status($programa->estado);
                            $opcion_arr = $this->app->get_ops($idopcion);
                            $estado = $opcion_arr[$get_estado];
                        }
                        
                        
                        
                        
                    }
                }
            }
        }


        $program_array = array(
            'identificador' => $ip,
            'titulo' => $titulo,
            'monto' => $monto,
            'estado' => $estado,
            'detalle' => "det",
            'id_dna' => $idrel,
            'fecha' => $fecha,
            'programa' => $programa->nombre);

        return $program_array;
    }

    function get_status($status_ctrl) {
        $this->db->select('*');
        $this->db->where('idpreg', $status_ctrl);
        $query = $this->db->get('preguntas');
        foreach ($query->result() as $row)
            $idopcion = $row->idopcion;
        return $idopcion;
    }

    function test_getvalue($id, $idframe, $tabledest) {


        $table = $tabledest;


        $this->db->select('valor');
        $this->db->where('idpreg', $idframe);
        $this->db->where('id', $id);
        $query = $this->db->get($table);
        $parameter = array();
        foreach ($query->result() as $row)
            return $row->valor;
    }

    function test_gethist($id, $idframe, $tabledest) {

        $tablahist = str_replace("td_", "th_", $tabledest);


        $this->db->select('fecha');
        $this->db->where('idpreg', $idframe);
        $this->db->where('id', $id);
        $query = $this->db->get($tablahist);
        $parameter = array();
        foreach ($query->result() as $row)
            $parameter[] = $row->fecha;

        return $parameter;
    }

    function search4rel($id, $idpregs, $table) {
        $rtnarr = array();
        foreach ($idpregs as $idpreg) {

            $this->db->select('idsent.id', $table);
            $this->db->join('idsent', 'idsent.id = ' . $table . '.id', 'inner');
            $this->db->where('idpreg', $idpreg);
            $this->db->where('estado', 'activa');
            $this->db->like('valor', trim($id));

            $query = $this->db->get($table);
            $parameter = array();
            foreach ($query->result() as $newrow) {
                $rtnarr[] = $newrow->id;
            }
        }
        return $rtnarr;
    }

    function search4relSelf($id, $idpregs, $table) {
        $rtnarr = array();
        foreach ($idpregs as $idpreg) {

            $this->db->select('idsent.id', $table);
            $this->db->join('idsent', 'idsent.id = ' . $table . '.id', 'inner');
            $this->db->where('idpreg', $idpreg);
            $this->db->where('estado', 'activa');
            $this->db->where($table . '.id', $id);

            $query = $this->db->get($table);
            $parameter = array();
            foreach ($query->result() as $newrow) {
                $rtnarr[] = $newrow->id;
            }
        }
        return $rtnarr;
    }

}

/* End of file webservice */
    /* Location: ./system/application/controllers/welcome.php */    