<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Empresas_remote extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('user');
        $this->load->model('app');          
        $this->user->authorize('modules/genias/controllers/genias');
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (float) $this->session->userdata('iduser');
        $this->containerEmpresas = 'container.empresas';
        $this->containerGenias = 'container.genias';
        $this->containerTablets = 'container.tablets';
        $this->containerTask = 'container.genias_tasks';
    }

    /* GENIAS */

    public function Insert() {
        $container = $this->containerEmpresas;
        $containerTask = $this->containerTask;

        $input = json_decode(file_get_contents('php://input'));

        foreach ($input as $thisform) {

            /* GENERO ID */
            $id = ($thisform->id == null || strlen($thisform->id) < 6 ) ? $this->app->genid($container) : $thisform->id;
            
            
            $thisform->status = 'activa';
            
            /* CHECKEO CUIT */
            $queryCuit = array('1695' => $thisform->{1695});
            $resultCuit = $this->mongo->db->$container->findOne($queryCuit);

            if ($resultCuit['id'] != null) {
                if ($thisform->{1695} != null) {
                    $id = $resultCuit['id'];
                }
            }

            /* IDENTIFICO TAREA */
            $getTask = (int) $thisform->task;
            /* Lo paso como Objeto */
            $thisform = (array) $thisform;
            $thisform['idu'] = (int) ($this->idu);

            /* Insert/Update dato de la empresa */
            $result = $this->app->put_array($id, $container, $thisform);          



            if ($result) {
                /* Update Task */
                $idTask = ($getTask == null || (int) ($getTask) < 6) ? $this->app->genid($containerTask) : $getTask;
                $queryTask = array('finalizada' => 1);
                $result = $this->app->put_array($idTask, $containerTask, $queryTask);
                
                $out = array('status' => 'ok');
            } else {
                $out = array('status' => 'error');
            }
        }
    }

    /*
     * VIEW
     */

    public function View() {

        $container = $this->containerEmpresas;
        $query = array('7406' => (int) ($this->idu));
        $resultData = $this->mongo->db->$container->find($query);

        foreach ($resultData as $returnData) {
            $fileArrMongo[] = $returnData;
        }
        //return $fileArrMongo;             

        if (!empty($fileArrMongo)) {
            echo json_encode(array(
                'success' => true,
                'message' => "Loaded data",
                'data' => $fileArrMongo
            ));
        }
    }

}