<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Menu extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->container = 'container.menu';
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        
    }

    //---add a path to repository
    function put_path($path = null, $properties = null) {
        if ($path) {
            $criteria = array_filter(array('path' => $path));

            $query = array('$set' => array('path' => $path, 'properties' => $properties));
            $options = array('upsert' => true, 'safe' => true);

            return $this->mongo->db->selectCollection($this->container)->update($criteria, $query, $options);
        }
    }

    //---add a path to repository
    function remove_path($path = null) {
        if ($path) {
            $criteria = array('path' => $path);
            $this->db->where($criteria);
            $this->db->delete($this->container);
            return true;
        }
    }

    
    function clear_paths($idgroup) {
        if ($idgroup) {
            $options = array("justOne" => false, "safe" => true);
            $criteria = array('idgroup' => (int) $idgroup);
            return $this->mongo->db->selectCollection($this->container)->remove($criteria, $options);
        } else {
            return false;
        }
    }

    function get_path($path) {
        if($path){
        $query=array('properties.id'=>$path);
        $rs = $this->mongo->db->selectCollection($this->container)->findOne($query);
        return $rs;
        } else {
            return null;
        }
    }
    function get_paths() {
        $query=array();
        $rs = $this->mongo->db->selectCollection($this->container)->find($query);
        $rtnArr = array();
        while ($arr = $rs->getNext()) {
            if (isset($arr['path']))
                $rtnArr[] = $arr['path'];
        }
        return $rtnArr;
    }
       function get_repository($query = array()) {
        //returns a mongo cursor with matching id's
        $rs = $this->mongo->db->selectCollection($this->container)->find($query);
        $rs->sort(array('path'));
        $repo = array();
        while ($r = $rs->getNext()) {
            $repo[$r['path']] = $r['properties'];
            //break;
        }
        return $repo;
    }
}