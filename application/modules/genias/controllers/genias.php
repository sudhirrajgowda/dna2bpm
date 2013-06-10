<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * genias
 *
 */
class Genias extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        $this->load->library('parser');
        $this->load->library('ui');
        $this->load->model('app');
        $this->load->model('user/user');
        $this->load->model('bpm/bpm');
        $this->load->module('bpm/engine');
        $this->load->model('user/rbac');
        $this->load->model('genias/genias_model');
        $this->load->helper('genias/tools');

        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'genias/';
        $this->user->authorize();
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = (float) $this->session->userdata('iduser');
    }

    function Index() {

        $customData = array();
        $customData['base_url'] = base_url();
        $customData['module_url'] = base_url() . 'genias/';
        $customData['js'] = array($this->module_url . "assets/jscript/dashboard.js" => 'Dashboard JS');
        $customData['goals'] = (array) $this->genias_model->get_goals($this->idu);

        // Projects
        $projects = $this->genias_model->get_config_item('projects');
        $customData['projects'] = $projects['items'];

        foreach ($this->genias_model->get_goals($this->idu) as $goal) {
            foreach ($customData['projects'] as $current) {
                if ($current['id'] == $goal['proyecto'])
                    $goal['proyecto_name'] = $current['name'];
            }
            $goal['cumplidas'] = 6;
            $metas_cumplidas = ($goal['cumplidas'] == $goal['cantidad']) ? (true) : (false);
            $goal['class'] = ($metas_cumplidas) ? ('well') : ('alert alert-info');

            $days_back = date('Y-m-d', strtotime("-5 day"));
            if (($goal['hasta'] < $days_back) && (!$metas_cumplidas))
                $goal['class'] = 'alert alert-error';
            $customData['goals'][] = $goal;
        }
        $this->render('dashboard', $customData);
    }

    function render($file, $customData) {

        $this->load->model('user/user');
        $this->user->authorize();
        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['nolayout'] = (in_array('nolayout', $segments)) ? '1' : '0';
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'idu' => $this->idu
        );
        $user = $this->user->get_user($this->idu);
        $cpData['user'] = (array) $user;
        $cpData['isAdmin'] = $this->user->isAdmin($user);
        $cpData['username'] = $user->lastname . ", " . $user->name;
        $cpData['username'] = $user->email;
        // Profile 
        $cpData['profile_img'] = get_gravatar($user->email);

        $cpData=  array_replace_recursive($customData,$cpData);
        $this->ui->compose($file, 'layout.php', $cpData);
    }

    //* ------ METAS ------ */

    function add_goal() {

        $this->user->authorize();
        $customData = $this->lang->language;
        $data = $this->input->post('data');
        $mydata = array(
            'idu' => $this->idu
        );
        foreach ($data as $k => $v) {
            $mydata[$v['name']] = $v['value'];
        }

        $date = date_create_from_format('d-m-Y', $mydata['desde']);
        $mydata['desde'] = date_format($date, 'Y-m-d');
        $date = date_create_from_format('d-m-Y', $mydata['hasta']);
        $mydata['hasta'] = date_format($date, 'Y-m-d');
        $mydata['id'] = $this->app->genid('container.genias_goals'); // create new ID 
        //@todo  COMPLETAR
        $mydata['genia'] = 'nombre de la GENIA'; //<<<<<--------------------
        $mydata['proyecto_nombre'] = 'nombre del proyecto'; //<<<<<--------------------
        //----genero un caso---------------------------------
        $idwf = 'genia_metas';
        $case = $this->bpm->gen_case($idwf);
        $mydata['case'] = $case;
        $id_goal = $this->genias_model->add_goal($mydata);
        //----------------------------------------------------
        $this->engine->Startcase('model', $idwf, $case, true);
        $thisCase = $this->bpm->get_case($case);
        $user = $this->user->get_user($this->idu);
        $thisCase['data'] = $mydata;
        $thisCase['data']['owner'] = (array) $user;



        //var_dump($case,$thisCase,$user);
        $this->bpm->save_case($thisCase);
        $this->engine->Run('model', $idwf, $case);
        //---la meta deberia estar disponible cuando este caso este en estado: finished
    }

    function programs() {
        $this->user->authorize();
        $customData = $this->lang->language;
        $this->render('programs', $customData);
    }

    /* ------ TAREAS ------ */

    // Render page
    function tasks() {
        $this->user->authorize();
        $customData = $this->lang->language;
        $customData['css'] = array($this->module_url . "assets/css/tasks.css" => 'Genias CSS');

        $projects = $this->genias_model->get_config_item('projects');
        $customData['projects'] = $projects['items'];
        $this->render('tasks', $customData);
    }

    function add_task() {
        $this->user->authorize();
        $customData = $this->lang->language;

        define('DURACION', 60);

        $serialized = $this->input->post('data');
        $mydata = compact_serialized($serialized);
        list($d, $m, $y) = explode("-", $mydata['dia']);
        $mydata['dia'] = iso_encode($mydata['dia']);
        $mydata['start'] = mktime($mydata['hora'], $mydata['minutos'], '00', $m, $d, $y);
        $mydata['end'] = mktime($mydata['hora'], $mydata['minutos'] + DURACION, '00', $m, $d, $y);
        $mydata['idu'] = $this->idu;
        $mydata['finalizada'] = (isset($mydata['finalizada'])) ? (1) : (0);

        if (!is_numeric($mydata['id'])) {
            $mydata['id'] = $this->app->genid('container.genias'); // create new ID    
        } else {
            $mydata['id'] = (integer) $mydata['id'];
        }



        $this->genias_model->add_task($mydata);
        echo json_encode($mydata);
    }

    function remove_task() {
        $id = $this->input->post('id');
        $this->genias_model->remove_task($id);
        echo "tasks.{$this->idu}.$id";
    }

    function get_tasks() {
        $proyecto = $this->uri->segment(3) ? $this->uri->segment(3) : 1;

        $tasks = $this->genias_model->get_tasks($this->idu, $proyecto);
        if (!$tasks->count())
            return;

        $mytasks = array();
        foreach ($tasks as $task) {
            $dia = iso_decode($task['dia']);
            $item = array(
                'id' => $task['id'],
                'title' => $task['title'],
                'start' => $task['start'],
                'end' => $task['end'],
                'allDay' => false,
                'detail' => $task['detail'],
                'dia' => $dia,
                'hora' => $task['hora'],
                'minutos' => $task['minutos'],
                'proyecto' => $task['proyecto'],
                'finalizada' => $task['finalizada']
            );

            $mytasks[] = $item;
        }

        echo json_encode($mytasks);
    }

    /* ------ MAP ------ */

    // Render page
    function map() {
        $this->user->authorize();
        $customData = $this->lang->language;
        $customData['css'] = array(
            $this->base_url . "map/assets/css/map.css" => 'Map CSS'
        );
        $customData['js'] = array(
            'http://maps.google.com/maps/api/js?sensor=true' => 'Google API',
            $this->base_url . 'map/assets/jscript/jquery.ui.map.v3/jquery.ui.map.full.min.js' => 'Jquery.ui.map V3',
            $this->module_url . 'assets/jscript/map/map.json.js' => 'Load Json Map',
        );
        $url=$this->module_url . 'assets/json/empresasGenia.json';
        $customData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
            'json_url' => $url,
        );
        $this->render('map', $customData);
    }

    /* ------ SCHEDULER ------ */

    // Render page
    function scheduler() {
        $this->user->authorize();
        $customData = $this->lang->language;
        $customData['js'] = array($this->module_url . "assets/jscript/scheduler.js" => 'Inicio Scheduler JS', $this->module_url . "assets/jscript/jquery-validate/jquery.validate.min.js" => 'Validate');
        $customData['css'] = array($this->module_url . "assets/css/genias.css" => 'Genias CSS');
        $projects = $this->genias_model->get_config_item('projects');
        $customData['projects'] = $projects['items'];
        //print_r($customData['projects']);
        $year = date('Y');
        $month = date('m');

        $this->render('scheduler', $customData);
    }

    /* ------ CONTACTS ------ */

    // Render page

    function contacts() {
        $this->user->authorize();
        $customData = $this->lang->language;
        $this->render('contacts', $customData);
    }

    /* ------ ??? ------ */

    function Form($parm = null) {

        //echo $this->idu;   
        //---Libraries
        $this->load->library('parser');
        $this->load->library('ui');

        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Escenario Pyme.';


        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/ext.data.js' => 'Base Data',
            $this->module_url . 'assets/jscript/form.js' => 'Objetos Custom D!',
            $this->module_url . 'assets/jscript/ext.viewport.js' => '',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Teststore() {
        //echo $this->idu;   
        //---Libraries
        $this->load->library('parser');
        $this->load->library('ui');

        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Test ofline json store<br/> <h3>mirá la consola</h3>';


        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/store-test/ext.data.js' => 'Base Data',
            $this->module_url . 'assets/jscript/store-test/start.js' => 'Start Test',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function Tablet() {
        //echo $this->idu;   
        //---Libraries
        $this->load->library('parser');
        $this->load->library('ui');

        $cpData = $this->lang->language;
        $segments = $this->uri->segment_array();
        $cpData['theme'] = $this->config->item('theme');
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'Formulario Control Stock Tablets | Genias.';


        $cpData['js'] = array(
            $this->module_url . 'assets/jscript/ext.data.tablet.js' => 'Base Data',
            $this->module_url . 'assets/jscript/form.tablet.js' => 'Objetos Custom D!',
            $this->module_url . 'assets/jscript/ext.viewport.js' => '',
        );

        $cpData['global_js'] = array(
            'base_url' => $this->base_url,
            'module_url' => $this->module_url,
        );
        $this->ui->makeui('ext.ui.php', $cpData);
    }

    function App() {
        /* REMOTE */
        echo '{"user":"' . $this->idu . '"}';
        echo $this->user->admin->showall($this->idu);
    }

    /* ------ CONFIG ------ */

    // Render page

    function qr($parameter = null) {

        $imgParameter = ($parameter == NULL) ? '5c-FF-35-7C-96-FB' : $parameter;
        $this->load->module('qr');
        echo $this->qr->gen($imgParameter);
    }

    function config() {
        $this->user->authorize();
        $customData = $this->lang->language;
        $projects = $this->genias_model->get_config_item('projects');
        $customData['js'] = array($this->module_url . "assets/jscript/config.js" => 'Config JS');
        $customData['projects'] = $projects['items'];

        $this->render('config', $customData);
    }

    // Change projects id from here
    function config_set_projects() {
        $this->user->authorize();
        $myProjects = $this->input->post('data');

        // Preparo array para la base
        $items = array();
        for ($i = 0; $i < count($myProjects); $i+=2) {
            $items[] = array('id' => (int) $myProjects[$i + 1]['value'], 'name' => $myProjects[$i]['value']);
        }
        $mydata = array('name' => 'projects', 'items' => $items);

        $error = $this->genias_model->config_set($mydata);
        echo (is_null($error)) ? ("Registro guardado") : ("No se ha podido guardar el registro");
    }

}

// close


