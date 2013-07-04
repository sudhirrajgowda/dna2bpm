<?php

/**
 * @class genia
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Genias_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->idu = (int) $this->session->userdata('iduser');
        /* Set locale to Spansih */

    }

    // ======= TAREAS ======= //

    function remove_task($id) {
        $container = 'container.genias_tasks';
        $query = array('id' => (integer) $id);
        $rs = $this->mongo->db->$container->remove($query);
        return $rs['err'];
    }

    function add_task($task) {
        $options = array('upsert' => true, 'safe' => true);
        $container = 'container.genias_tasks';
        $query = array('id' => (integer) $task['id']);
        $rs = $this->mongo->db->$container->update($query, $task, $options);
        return $rs['err'];
    }

    function get_tasks($idu, $proyecto) {

        //$query = array('idu' => (int) $idu, 'proyecto' => $proyecto);

        $container = 'container.genias_tasks';
        $genias = $this->get_genia($idu); 
        $idus=array($idu);
        if($genias!==false){ // 
            foreach($genias['genias'] as $genia){
                if($genias['rol']=='coordinador'){
                    //$query = array('idu' => array('$in'=>$genia['users']),'idu' => (double) $idu);
                    $idus=array_merge($genia['users'],$idus);
                }

            }
        }
        $query = array('idu' => array('$in'=>$idus), 'proyecto' => $proyecto); 
        
        
        $result = $this->mongo->db->$container->find($query)->sort(array('id' => -1));

        //var_dump($result, json_encode($result), $result->count());
        return $result;
    }

    // ======= METAS ======= //

    function add_goal($goal) {
        $options = array('upsert' => true, 'safe' => true);
        $container = 'container.genias_goals';
        $query = array('id' => (integer) $goal['id']);
        return $this->mongo->db->$container->update($query, $goal, $options);
    }

    function get_goals($idu) {
        $container = 'container.genias_goals';
        $this->lang->load('calendar', $this->config->item('language'));
        $genias = $this->get_genia($idu); 
        $idus=array($idu);
        // Por cada Genia

        if($genias!==false){ // 
            foreach($genias['genias'] as $genia){
                if($genias['rol']=='coordinador'){
                    //$query = array('idu' => array('$in'=>$genia['users']),'idu' => (double) $idu);
                    $idus=array_merge($genia['users'],$idus);
                }

            }
        }
        $query = array('idu' => array('$in'=>$idus));          
        $goals = $this->mongo->db->$container->find($query)->sort(array('desde' => -1));
        $result=array();
        while($mygoals=$goals->getnext()){
        // Mes 
        $date = date_create_from_format('Y-m-d', $mygoals['desde']);
        $mes='cal_'.strtolower(date_format($date, 'F'));
        $mygoals['desde']=$this->lang->line($mes);

        $result[]=$mygoals;
        }

        return $result;
    }

    function get_case($case) {
        $query = array('id' => $case);
        $container = 'case';
        $result = $this->mongo->db->$container->findOne($query);
        return $result;
    }

    // ======= CONFIG ======= //

    function get_config_item($name) {
        $container = 'container.genias_config';
        $query = array('name' => $name);
        $result = $this->mongo->db->$container->findOne($query);
        return $result;
    }

    function config_set($data) {
        $container = 'container.genias_config';
        $options = array('upsert' => true, 'safe' => true);
        $query = array('name' => 'projects');
        $rs = $this->mongo->db->$container->update($query, $data, $options);
        return $rs['err'];
    }

    /*RETURN EMPRESAS*/
    function get_empresas($query) {
        $rtn = array();
        $query['status'] = 'activa';
        $fields = array('id',
            'status'
            , '1693'  //     Nombre de la empresa
            , '1695'  //     CUIT
            , '7819' // 	Longitud
            , '7820' // 	Latitud
            , '4651' // 	Provincia
            , '4653' //     Calle Ruta
            , '4654' //     Nro /km
            , '4655' //     Piso
            , '4656' //     Dto Oficina
            , '1699' // 	Partido            
            );
        $container = 'container.empresas';
        $result = $this->mongo->db->$container->find($query, $fields);
        $result->limit(2000);
        foreach ($result as $empresa) {
            unset($empresa['_id']);
            $rtn[] = $empresa;
        }
        return $rtn;
    }
    
    /*RETURN VISITAS*/
    function get_visitas($query, $idu) {
        $rtn = array();        
        $query['idu'] = (int)$idu;
        $fields = array('id',
            'cuit'
            , 'fecha'  //     Fecha
            , 'nota'  //     Nota            
            );
        $container = 'container.genias_visitas';
        $result = $this->mongo->db->$container->find($query, $fields);
        $result->limit(2000);
        foreach ($result as $visita) {
            unset($visita['_id']);
            unset($visita['id']);
            $rtn[] = $visita;
        }
        return $rtn;
    }
    
    //======== Actualiza Meta Activa =========//

    function goal_update($proyecto='2',$id_visita=null){
        $container_metas = 'container.genias_goals';
        //----busco meta activa
        $query=array(
            'proyecto'=>$proyecto,
            'idu'=>$this->idu,
            'hasta'=>array('$lte'=>date('Y-m-t')),
            'desde'=>array('$gte'=>date('Y-m-01')),
            );
        //echo json_encode($query);exit;
        $metas=$this->mongo->db->$container_metas->find($query);
        foreach($metas as $meta){
            $case=$this->get_case($meta['case']);
            if($case['status']=='closed'){
                break;
            }
        }
        //var_dump($query,$meta);exit;
        if(isset($meta)){

        //----Agrego visita a la meta
            $meta['cumplidas'][]=$id_visita;
            $meta['cumplidas']=array_filter(array_unique($meta['cumplidas']));
            $this->mongo->db->$container_metas->save($meta);
        }
    }


    // ======= USER CONTROL ======= //
    
    function get_genia($idu){
        $container = 'container.genias';

        // Es coordinador?    
        $query=array('coordinadores'=>((int)$idu));
        $result = $this->mongo->db->$container->find($query); 

        $genias=array();
        $rol='';
        while ($r = $result->getNext()) {
            $rol='coordinador';
            $my_genias[]=$r;
            //var_dump($r['_id']);

        }
        
        if($rol=='coordinador'){
            $genias['rol']=$rol;
            $genias['genias']=$my_genias;
            return $genias; 
        }

        // Es usuario?
        $query=array('users'=>(int)$idu);
        $result = $this->mongo->db->$container->find($query);
        while ($r = $result->getNext()) {
            $rol='user';
            $my_genias[]=$r;
        }
        
        if($rol=='user'){
            $genias['rol']=$rol;
            $genias['genias']=$my_genias;
            return $genias; 
        }   
        
        return false;
    }

}
