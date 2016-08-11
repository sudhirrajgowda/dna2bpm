<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * inventory
 * 
 * Description of the class
 * 
 * @author Martin González 
 * @date    Apr 20, 2015
 */
class fonapyme_forment extends MX_Controller {

    function __construct() {
        parent::__construct();
         $this->load->model('user/user');
         $this->load->model('user/group');
         $this->load->model('fonapyme_model_forment');
         $this->user->authorize('modules/fonapyme');
         $this->load->library('parser');
//         $this->load->library('ui');
// //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . 'bonita/';
// ;
// //----LOAD LANGUAGE
         $this->idu = (float) $this->session->userdata('iduser');
// //---config
         $this->load->config('fonapyme/config');
// //---QR
         //$this->load->module('qr');
    }

    /*
     * Presentamos menu de acciones: info Checkin
     */

    function Index(){

     	//Modules::run('dashboard/dashboard','inventory/json/inventory.json');
	$this->user->authorize();
	$this->load->module('dashboard');
	$this->dashboard->dashboard('fonapyme/json/fonapyme_forment.json');
    
    }
    
    
    function descarga() {
        $model='fonapyme_model_forment';
        
        $customData = array();
        
        $customData['base_url'] = $this->base_url;
       
        
        return $this->parser->parse('fonapyme/forment_descarga_view',$customData,true,true);
    }
    
    function descarga1() {
        $model='fonapyme_model_forment';
    
        $customData = array();
        
        $customData['base_url'] = $this->base_url;
       
        $result = array();
        $registros = array();
        //$result['tabla'] = '<H3>HOLA</H3>';
        $tabla = '';
        $tabla = $tabla.'<tr><td>';
        $tabla = $tabla.'Fecha Hora Carga</td><td>';
        $tabla = $tabla.'Calificaión</td><td>';
        $tabla = $tabla.'Prestamo</td><td>';
        $tabla = $tabla.'Monto</td><td>';
        $tabla = $tabla.'Situación</td><td>';
        $tabla = $tabla.'Vinculación</td><td>';
        $tabla = $tabla.'Nombre</td><td>';
        $tabla = $tabla.'Forma</td><td>';
        $tabla = $tabla.'Cuit</td><td>';
        $tabla = $tabla.'Provincia</td><td>';
        $tabla = $tabla.'Municipio</td><td>';
        $tabla = $tabla.'Dirección</td><td>';
        $tabla = $tabla.'Contacto</td><td>';
        $tabla = $tabla.'Cargo</td><td>';
        $tabla = $tabla.'Teléfono</td><td>';
        $tabla = $tabla.'Email</td><td>';
        $tabla = $tabla.'Email1</td><td>';
        $tabla = $tabla.'Tipo Préstamo</td><td>';
        $tabla = $tabla.'Monto Total</td><td>';
        $tabla = $tabla.'Financiamiento </td><td>';
        $tabla = $tabla.'Balance 1</td><td>';
        $tabla = $tabla.'Balance 2</td><td>';
        $tabla = $tabla.'Balance 2</td><td>';
        $tabla = $tabla.'Fecha AFIP</td><td>';
        $tabla = $tabla.'Sector</td><td>';
        $tabla = $tabla.'Codigo</td><td>';
        $tabla = $tabla.'Descripción</td><td>';
        $tabla = $tabla.'Error: Menor a dos años</td><td>';
        $tabla = $tabla.'Error: Menor a dos balances</td><td>';
        $tabla = $tabla.'Error: Excede facturación</td></tr>';
        $this->load->model($model);
        $registros = $this->fonapyme_model_forment->download_registros();
        
        foreach($registros as $download){
            $tabla = $tabla.'<tr><td>';
            $tabla = $tabla.$download['fecha']['year'].'-'.$download['fecha']['mon'].'-'.$download['fecha']['mday'].' '.$download['fecha']['hours'].':'.$download['fecha']['minutes'].':'.$download['fecha']['seconds'].'</td>';
            $tabla = $tabla.'<td>'.$download['clasifica'].'</td>';
            $i=0;
            for($i=0; $i<21 ; $i++){
                $tabla = $tabla.'<td>'.$download['fields'][$i]['value'].'</td>';
            } 
            $i=0;
            for($i=0; $i<16 ; $i++){
                $tabla = $tabla.'<td>'.$download['fields2'][$i]['value'].'</td>';
            }
            $i--;
            
            if (isset($download['fields2'][$i]['detalle'])){
            $tabla = $tabla.'<td>'.$download['fields2'][$i]['detalle'].'</td>';
            }else{
            $tabla = $tabla.'<td></td>';    
                
                
            }
            if (isset($download['error_dos_y'])){
            $tabla = $tabla.'<td>'.$download['error_dos_y'].'</td>';
            }else{
            $tabla = $tabla.'<td></td>'; 
            }
            if (isset($download['error_dos_balances'])){
            $tabla = $tabla.'<td>'.$download['error_dos_balances'].'</td>';
            }else{
            $tabla = $tabla.'<td></td>'; 
            }
            if (isset($download['error_balance_prom'])){
            $tabla = $tabla.'<td>'.$download['error_balance_prom'].'</td>';
            }else{
            $tabla = $tabla.'<td></td>'; 
            }
            if (isset($download['programa'])){
            $tabla = $tabla.'<td>'.utf8_decode($download['programa']).'</td>';
            }else{
            $tabla = $tabla.'<td></td>'; 
            }
            
            
            
            $tabla = $tabla.'</tr>';
        }
        
        //$tabla = '<tr><td>1</td><td>2</td></tr>';
        $customData['tabla']=$tabla;
        
        $hoy = getdate();
     
        $new_filename = $hoy['year'] . '-';
        if ($hoy['mon'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mon'] . '-';
        else
            $new_filename = $new_filename . $hoy['mon'] . '-';

        if ($hoy['mday'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mday'] . '-';
        else
            $new_filename = $new_filename . $hoy['mday'] . '-';

        if ($hoy['hours'] < 10)
            $new_filename = $new_filename . '0' . $hoy['hours'] . '-';
        else
            $new_filename = $new_filename . $hoy['hours'] . '-';

        if ($hoy['minutes'] < 10)
            $new_filename = $new_filename . '0' . $hoy['minutes'] . '-';
        else
            $new_filename = $new_filename . $hoy['minutes'] . '-';

        if ($hoy['seconds'] < 10)
            $new_filename = $new_filename . '0' . $hoy['seconds'] . '-Fonapyme-';
        else
            $new_filename = $new_filename . $hoy['seconds'] . '-Fonapyme-';
        
        $new_filename = $new_filename . rand(1000, 5000) .'.xls';
        
        $customData['new_filename'] = $new_filename;
        
        //$result['tabla'] = $this->parser->parse('fonapyme/forment_descarga_view1',$customData,true,true);
        
        
        echo $this->parser->parse('fonapyme/forment_descarga_view1',$customData,true,true);
        //$result = 'Prueba'; 
        
        //echo json_encode($result);
        //return $result;
    }
    
    
    
    
    
    
    /*
 
    function bonita_reporte_provincias(){

     	//Modules::run('dashboard/dashboard','inventory/json/inventory.json');
	$this->user->authorize();
	//$this->load->module('dashboard');
      //  $this->dashboard->dashboard('bonita/json/bonita_reporte_provincias.json');
    Modules::run('dashboard/dashboard', 'bonita/json/bonita_reporte_provincias.json');
        
    }
    
    function bonita_reporte_regiones(){

     	//Modules::run('dashboard/dashboard','inventory/json/inventory.json');
	$this->user->authorize();
	$this->load->module('dashboard');
        $this->dashboard->dashboard('bonita/json/bonita_reporte_regiones.json');
    }
    
    function bonita_reporte_sectores(){

     	//Modules::run('dashboard/dashboard','inventory/json/inventory.json');
	$this->user->authorize();
	$this->load->module('dashboard');
        $this->dashboard->dashboard('bonita/json/bonita_reporte_sector.json');
    }
    
    function bonita_reporte_sectores_tam(){

     	//Modules::run('dashboard/dashboard','inventory/json/inventory.json');
	$this->user->authorize();
	$this->load->module('dashboard');
        $this->dashboard->dashboard('bonita/json/bonita_reporte_sector_tam.json');
    }
    
    function Query() {
        $cpData['base_url'] = $this->base_url;
        $cpData['module_url'] = $this->module_url;
        $cpData['title'] = 'QR Code';
        $cpData['reader_title'] = $cpData['title'];
        $cpData['reader_subtitle'] = 'Read QR Codes from any HTML5 enabled device';
//         $cpData['css'] = array(
//             $this->base_url . "inventory/assets/css/inventory.css" => 'custom css',
//         );
//         $cpData['js'] = array(
//             $this->base_url . "qr/assets/jscript/html5-qrcode.min.js" => 'HTML5 qrcode',
//             $this->base_url . "qr/assets/jscript/jquery.animate-colors-min.js" => 'Color Animation',
//             $this->base_url . "inventory/assets/jscript/qr.js" => 'Main functions'
//         );

//         $cpData['global_js'] = array(
//             'base_url' => $this->base_url,
//             'module_url' => $this->module_url,
//             'redir' => $this->module_url . 'info'
//         );
       
//        $cpData['myjs']='<script type="text/javascript" src="'.$this->base_url.'qr/assets/jscript/html5-qrcode.min.js"></script>';
//        $cpData['myjs'].='<script type="text/javascript" src="'.$this->base_url.'qr/assets/jscript/jquery.animate-colors-min.js"></script>';
//        $cpData['myjs'].='<script type="text/javascript" src="'.$this->base_url.'inventory/assets/jscript/qr.js"></script>';
        echo $this->parser->parse('query', $cpData, true,true);  
//               $this->load->library('ui');
//               echo $this->ui->compose('query', 'bootstrap.ui.php', $cpData);


    }

    function bonita_filtros_provincia() {
        $model='model_bonita';
        
        $customData = array();
        
        $customData['base_url'] = $this->base_url;
       
        
        return $this->parser->parse('bonita/bonita_provincias_rep_view',$customData,true,true);
    }
    
    function bonita_filtros_region() {
        $model='model_bonita';
        
        $customData = array();
        
        $customData['base_url'] = $this->base_url;
       
        
        return $this->parser->parse('bonita/bonita_region_rep_view',$customData,true,true);
    }
    
    function bonita_filtros_sector() {
        $model='model_bonita';
        
        $customData = array();
        
        $customData['base_url'] = $this->base_url;
       
        
        return $this->parser->parse('bonita/bonita_sector_rep_view',$customData,true,true);
    }
    
    function bonita_filtros_sector_tam() {
        $model='model_bonita';
        
        $customData = array();
        
        $customData['base_url'] = $this->base_url;
       
        
        return $this->parser->parse('bonita/bonita_sector_tam_rep_view',$customData,true,true);
    }
   
    
    function bonita_tabla_provincia($desde,$hasta){
        $model='Model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $provincias = $this->load->model($model)->lista_provincias();
        $prestamos_total = $this->load->model($model)->prestamos_total($desde,$hasta);
        $monto_total = $this->load->model($model)->monto_total($desde,$hasta);
        
        
        
        $i = 0;
        $tablatotal = array();
        
        foreach ($provincias as $res){
            
            //$tabla = $tabla.'<tr><td>'.$res['nombre'].'</td></tr>';
            $tablatotal[$i] = $this->load->model($model)->montos_provincias($res['nombre'],$prestamos_total,$monto_total,$desde,$hasta);
            
            $i++;
            
            
        }
        
        $campos = array('provincia','prestamos_prov','porcent_prestamos' ,'montos_prov','porcent_montos');
        //var_dump($tablatotal[0][$campos[0]]);
        //exit();
        
        $tabla = ''; 
        $i = 0;
        $j = 0;
        //$tabla = $tabla.'<tr><a href="'.$this->module_url.'poa_report/poa_detail/" target="_blank">Exportar</a></tr>';
        
        for($i=0;$i<23;$i++){
            $tabla = $tabla.'<tr>';
            
            for($j=0;$j<5;$j++){
                if($campos[$j] == 'provincia'){
                    //$var['series'][$i]['name']
                    $var['xAxis']['categories'][$i] = $tablatotal[$i][$campos[$j]];
                }
                if($campos[$j] == 'montos_prov'){
                    $var['series'][0]['data'][$i] = floatval($tablatotal[$i][$campos[$j]]);
                }*//*
                if($campos[$j] == 'porcent_montos'){
                    $var['series'][1]['data'][$i] = intval($tablatotal[$i][$campos[$j]]);
                }*/
             /*   if($campos[$j] == 'montos_prov' || $campos[$j] == 'porcent_montos'){
                $tabla = $tabla.'<td>'.number_format((float)($tablatotal[$i][$campos[$j]]), 2, ',','.').'</td>';
                    
                }
                else{
                $tabla = $tabla.'<td>'.$tablatotal[$i][$campos[$j]].'</td>';
                }
            }
            $tabla = $tabla.'</tr>';
        }      
        
        $var['chart']['type'] = 'bar';
        $var['title']['text'] = 'Provincias';
         
        $var['series'][0]['name'] = ['Monto Otorgado'];
        //$var['series'][1]['name'] = ['Pymes Atendidas'];
        $var['yAxis']['title'] = 'Provincias';
        
        $customData['tabla'] = $tabla;
        $customData['url']=$this->module_url;
        
        $return['tabla'] = $this->parser->parse('bonita/bonita_provincias_rep_table_view',$customData,true,true);
        $return['grafico'] = $var; 
        echo json_encode($return);
        return $return;
       
    }
    
    function bonita_tabla_region($desde,$hasta){
        $model='Model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $provincias = $this->load->model($model)->lista_provincias();
        $prestamos_total = $this->load->model($model)->prestamos_total($desde,$hasta);
        $monto_total = $this->load->model($model)->monto_total($desde,$hasta);
        
        $reg = array();
        
        $reg['centro']['nombre'] = 'CENTRO';
        $reg['nea']['nombre'] = 'NEA';
        $reg['noa']['nombre'] = 'NOA';
        $reg['pat']['nombre'] = 'PATAGONIA';
        $reg['cuyo']['nombre'] = 'CUYO';
        $i = 0;
        $j = 0;
        $tablatotal = array();
        $reg_name = array('centro','nea','noa','pat','cuyo');
        $campos = array('nombre','prestamos_prov','montos_prov','cant_norm','monto_norm','relacion');
        for($j=0;$j<5;$j++){
            for($i=1; $i <6; $i++){
                $reg[$reg_name[$j]][$campos[$i]] = 0;
            }
        }
        
        $i = 0;
        foreach ($provincias as $res){
            
            //$tabla = $tabla.'<tr><td>'.$res['nombre'].'</td></tr>';
            $tablatotal[$i] = $this->load->model($model)->montos_provincias($res['nombre'],$prestamos_total,$monto_total,$desde,$hasta);
            
            //CENTRO
            if($tablatotal[$i]['provincia'] == "BUENOS AIRES" || $tablatotal[$i]['provincia']== "CORDOBA" || $tablatotal[$i]['provincia']== "ENTRE RIOS" || $tablatotal[$i]['provincia']== "LA PAMPA" ||  $tablatotal[$i]['provincia']=="SANTA FE" || $tablatotal[$i]['provincia']=="CABA"){
               $reg['centro']['prestamos_prov'] = $reg['centro']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['centro']['porcent_prestamos'] = $reg['centro']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['centro']['montos_prov'] = $reg['centro']['montos_prov'] + $tablatotal[$i]['montos_prov'];
               //$reg['centro']['porcent_montos'] =  $reg['centro']['porcent_montos'] + $tablatotal[$i]['porcent_montos'];
            }
            //NEA
            
            if($tablatotal[$i]['provincia'] == "CHACO" || $tablatotal[$i]['provincia']== "CORRIENTES" || $tablatotal[$i]['provincia']== "FORMOSA" || $tablatotal[$i]['provincia']== "MISIONES"){
               $reg['nea']['prestamos_prov'] = $reg['nea']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['nea']['porcent_prestamos'] = $reg['nea']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['nea']['montos_prov'] = $reg['nea']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              // $reg['nea']['porcent_montos'] = $reg['nea']['porcent_montos'] + $tablatotal[$i]['porcent_montos'];
            }
            //NOA
            if($tablatotal[$i]['provincia'] == "CATAMARCA" || $tablatotal[$i]['provincia']== "JUJUY" || $tablatotal[$i]['provincia']== "LA RIOJA" || $tablatotal[$i]['provincia']== "SALTA" ||  $tablatotal[$i]['provincia']=="SANTIAGO DEL ESTERO" ||  $tablatotal[$i]['provincia']=="TUCUMAN"){
               $reg['noa']['prestamos_prov'] =  $reg['noa']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['noa']['porcent_prestamos'] =  $reg['noa']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['noa']['montos_prov'] =  $reg['noa']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              // $reg['noa']['porcent_montos'] =  $reg['noa']['porcent_montos'] + $tablatotal[$i]['porcent_montos']; 
            }
            //PAT
            if($tablatotal[$i]['provincia'] == "CHUBUT" || $tablatotal[$i]['provincia']== "NEUQUEN" || $tablatotal[$i]['provincia']== "RIO NEGRO" || $tablatotal[$i]['provincia']== "SANTA CRUZ" ||  $tablatotal[$i]['provincia']=="TIERRA DEL FUEGO"){
               $reg['pat']['prestamos_prov'] =  $reg['pat']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['pat']['porcent_prestamos'] =  $reg['pat']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['pat']['montos_prov'] =  $reg['pat']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              // $reg['pat']['porcent_montos'] =  $reg['pat']['porcent_montos'] + $tablatotal[$i]['porcent_montos']; 
            }
            //CUYO
            if($tablatotal[$i]['provincia'] == "MENDOZA" || $tablatotal[$i]['provincia']== "SAN JUAN" || $tablatotal[$i]['provincia']== "SAN LUIS" ){
                $reg['cuyo']['prestamos_prov'] = $reg['cuyo']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              //  $reg['cuyo']['porcent_prestamos'] = $reg['cuyo']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
                $reg['cuyo']['montos_prov'] = $reg['cuyo']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              //  $reg['cuyo']['porcent_montos'] = $reg['cuyo']['porcent_montos'] + $tablatotal[$i]['porcent_montos']; 
            }
            
            
            
            
            
            
            $i++;
                        
        }
        //var_dump($tablatotal[0][$campos[0]]);
        //exit();
        
        $tabla = ''; 
        $i = 0;
        $j = 0;
        //$tabla = $tabla.'<tr><a href="'.$this->module_url.'poa_report/poa_detail/" target="_blank">Exportar</a></tr>';
        
        $cant_normalizado = array(698160,36088,43453,42634,51129);
        $cant_normalizado_total = 828830;
        $montos_prov_total = 0;
        $prestamos_prov_total = 0;
        $cant_norm_total = 0;
        $monto_norm_total =0;
        $relacion_total = 0;
        $array = array();
        
        // Totales
        
        
        for($i =0; $i<5;$i++){ 
            
           
            for($j=0;$j<6;$j++){
                if($campos[$j] == 'nombre'){
                    
                   
                }
                if($campos[$j] == 'prestamos_prov'){
                    $prestamos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $prestamos_prov_total = $prestamos_prov_total + $prestamos_prov;
                }
                if($campos[$j] == 'montos_prov'){
                    $montos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $montos_prov_total = $montos_prov_total + $montos_prov;
                }
                if($campos[$j] == 'cant_norm'){
                    $cant_norm = $cant_normalizado[$i]/1000;
                    $cant_norm_total = $cant_norm_total + $cant_norm;
                }
                if($campos[$j] == 'monto_norm'){
                    $monto_norm = $reg[$reg_name[$i]][$campos[2]] / $cant_normalizado[$i];
                    $monto_norm_total = $monto_norm_total + $monto_norm;
                    
                } 
            }
            
            
            
        }
        
        // Armo la tabla y el array para el gráfico
        for($i =0; $i<5;$i++){ 
            $tabla = $tabla.'<tr>';
           
            for($j=0;$j<6;$j++){
                if($campos[$j] == 'nombre'){
                    
                    $var['xAxis']['categories'][$i] = $reg[$reg_name[$i]][$campos[$j]];
                    $tabla = $tabla.'<td>'.$reg[$reg_name[$i]][$campos[$j]].'</td>';
                }
                if($campos[$j] == 'prestamos_prov'){
                    $prestamos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $tabla = $tabla.'<td>'.$prestamos_prov.'</td>';
                    //$prestamos_prov_total = $prestamos_prov_total + $prestamos_prov;
                }
                if($campos[$j] == 'montos_prov'){
                    $montos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $tabla = $tabla.'<td>'.number_format((float)($montos_prov), 2, ',','.').'</td>';
                    //$montos_prov_total = $montos_prov_total + $montos_prov;
                }
                
                if($campos[$j] == 'cant_norm'){
                    $cant_norm = $cant_normalizado[$i]/1000;
                    $tabla = $tabla.'<td>'.number_format((float)($cant_norm), 0, ',','.').'</td>';
                    //$cant_norm_total = $cant_norm_total + $cant_norm;
                }
                
                if($campos[$j] == 'monto_norm'){
                    $monto_norm = $reg[$reg_name[$i]][$campos[2]] / $cant_normalizado[$i];
                    $tabla = $tabla.'<td>'.number_format((float)($monto_norm), 2, ',','.').'</td>';
                    $var['series'][0]['data'][$i] = ($monto_norm);
                    //$monto_norm_total = $monto_norm_total + $monto_norm;
                    
                } 
                
                if($campos[$j] == 'relacion' ){
                    $relacion =  ($monto_norm)/(($montos_prov_total)/($cant_normalizado_total));   
                    $tabla = $tabla.'<td>%'.number_format((float)($relacion), 2, ',','.').'</td>';
                    $relacion_total = $relacion_total +$relacion;
                }
                
            }
            $tabla = $tabla.'</tr>';
              
        }
        $tabla = $tabla.'<tr><th>TOTALES:</th><TH>'.$prestamos_prov_total.'</TH><TH>'.number_format((float)($montos_prov_total), 2, ',','.').
                        '</TH><TH>'.number_format((float)($cant_normalizado_total/1000), 0, ',','.').'</TH><TH>'.number_format((float)($monto_norm_total), 2, ',','.').'</TH>
                        <TH></TH>
                    </tr>';
        //var_dump($tabla);
        //exit();
        
        $var['chart']['type'] = 'bar';
        $var['title']['text'] = 'Regiones';
         
        $var['series'][0]['name'] = ['Monto Otorgado'];
        $var['yAxis']['title'] = 'Regiones';
        
        $customData['tabla'] = $tabla;
        $customData['url']=$this->module_url;
        
        $return['tabla'] = $this->parser->parse('bonita/bonita_regiones_rep_table_view',$customData,true,true);
        $return['grafico'] = $var; 
        echo json_encode($return);
        return $return;
        
        
    }
    
    function bonita_tabla_sector($desde,$hasta){
        $model='model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $sectores = $this->load->model($model)->sectores();
        
        
        $totales['capital'] = 0;
        $totales['cantidad'] = 0;
        $totales['porcent_cant'] = 0;
        $totales['porcent_capital'] = 0;
        $z = 0;
        $datos_sectores = array();
        $tabla = ''; 
        foreach($sectores as $sect){
           
            $datos_sectores[$z] = $this->load->model($model)->datos_sectores($sect['sector'],$desde,$hasta);
            $totales['capital'] =  $totales['capital'] + $datos_sectores[$z]['capital'];
            $totales['cantidad'] = $totales['cantidad'] + $datos_sectores[$z]['cantidad'];
            $z++;
        }
        
        $i = 0;
        $j = 0;
        
        $campos =array('sector','cantidad','porcent_cant','capital','porcent_capital');
        for($i=0;$i<$z;$i++){
            $tabla = $tabla.'<tr>';
            
            for($j=0;$j<5;$j++){
                
                
                switch($campos[$j]){
                    case 'sector':
                        $tabla = $tabla.'<td>'.$datos_sectores[$i][$campos[$j]].'</td>';
                        $var['xAxis']['categories'][$i] = $datos_sectores[$i][$campos[$j]];
                        break;
                    case 'cantidad':
                        $tabla = $tabla.'<td>'.number_format((float)($datos_sectores[$i][$campos[$j]] ), 0, ',','.').'</td>';
                        break;
                    case 'porcent_cant':
                        $tabla = $tabla.'<td>%'.number_format((float)(($datos_sectores[$i][$campos[$j - 1]]/ $totales['cantidad'])*100), 2, ',','.').'</td>';
                        $totales['porcent_cant'] = $totales['porcent_cant'] + (($datos_sectores[$i][$campos[$j - 1]]/ $totales['cantidad'])*100);
                        break;
                    case 'capital':
                        $tabla = $tabla.'<td>$'.number_format((float)($datos_sectores[$i][$campos[$j]]), 2, ',','.').'</td>';
                        $var['series'][0]['data'][$i] = (float)($datos_sectores[$i][$campos[$j]]);
                        break;
                    case 'porcent_capital':
                        $tabla = $tabla.'<td>%'.number_format((float)((($datos_sectores[$i][$campos[$j -1]] / $totales['capital'])*100)), 2, ',','.').'</td>';
                        $totales['porcent_capital'] =$totales['porcent_capital'] + (($datos_sectores[$i][$campos[$j -1]] / $totales['capital'])*100); 
                        break;
               
                } 
            }
            $tabla = $tabla.'</tr>';
        }      
        
        
        $tabla = $tabla.'<tr><th>Totales:</th><TH>'.number_format((float)($totales['cantidad']), 0, ',','.').'</TH>
        <TH>%'.number_format((float)($totales['porcent_cant']), 2, ',','.').'</TH>
        <TH>$'.number_format((float)($totales['capital']), 0, ',','.').'</TH>
        <TH>%'.number_format((float)($totales['porcent_capital']), 2, ',','.').'</TH></tr>';
        
        
        
         
        $var['chart']['type'] = 'bar';
        $var['title']['text'] = 'Sectores';
         
        $var['series'][0]['name'] = ['Monto Otorgado'];
        //$var['series'][1]['name'] = ['Pymes Atendidas'];
        $var['yAxis']['title'] = 'Sectores';
        
        $customData['tabla'] = $tabla;
        $customData['url']=$this->module_url;
        
        $return['tabla'] = $this->parser->parse('bonita/bonita_sector_rep_table_view',$customData,true,true);
        $return['grafico'] = $var; 
        echo json_encode($return);
        return $return;
       
    }
 
 
    function bonita_tabla_sector_tam($desde,$hasta){
        $model='model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $sectores = $this->load->model($model)->sectores();
        $tamanios = $this->load->model($model)->tamanios();
        $z = 0; //sector
        $y = 0; //tamaño
        //$totales['capital'] = array();
        //$totales['capital'][]
        //$totales['cantidad'][$z] = 0;
        //$totales['porcent_cant'] = 0;
        //$totales['porcent_capital'] = 0;
       
        $datos_sectores = array();
        $tabla = '';
        $tabla1 = ''; 
        foreach($sectores as $sect){
            $totales['capital'][$sect['sector']]=0;
           foreach($tamanios as $tams){
                $totales['capital'][$tams['tam_empresa']]=0;
                $datos_sectores[$z] = $this->load->model($model)->datos_sectores_tams($sect['sector'],$tams['tam_empresa'],$desde,$hasta);
            //$totales['capital'][$sect['sector']] = $totales['capital'][$sect['sector']] + $datos_sectores[$z]['capital'];
            
            
            //$totales['capital'][$z] =  $totales['capital'][$z] + $datos_sectores[$z][$y]['capital'];
            //$totales['cantidad'][$z] = $totales['cantidad'][$z] + $datos_sectores[$z][$y]['cantidad'];
            $z++;
           }////////////ACAAAAAAAAAAA
            
        }
        
        $i = 0;
        $j = 0;
        $tam_et = array('','Micro','Pequeña','Mediana','Grande');
        $campos = array('sector','tam_empresa','cantidad','capital','porcent_total');
        $r=0;
        $s=0;
        $t=0;
        $u=0;
        
        $var['series'][0]['data'][$r] = (float)(0);
        $var['series'][0]['name'] = ['Micro'];
        $var['series'][1]['data'][$s] = (float)(0);
        $var['series'][1]['name'] = ['Pequeña'];
        $var['series'][2]['data'][$t] = (float)(0);
        $var['series'][2]['name'] = ['Mediana'];
        $var['series'][3]['data'][$u] = (float)(0);
        $var['series'][3]['name'] = ['Grande'];
        
        
        for($i=0;$i<$z;$i++){
            $tabla = $tabla.'<tr>';
            
            for($j=0;$j<5;$j++){
                switch($campos[$j]){
                    case 'sector':
                        $tabla = $tabla.'<th>'.$datos_sectores[$i][$campos[$j]].'</th>';
                        $var['xAxis']['categories'][$i] = $datos_sectores[$i][$campos[$j]];
                        break;
                    case 'tam_empresa':
                        $tabla = $tabla.'<td>'.$tam_et[$datos_sectores[$i][$campos[$j]]].'</td>';
                        break;
                    case 'cantidad':
                        $tabla = $tabla.'<td>'.number_format((float)($datos_sectores[$i][$campos[$j]]), 0, ',','.').'</td>';
                        //$totales['porcent_cant'] = $totales['porcent_cant'] + (($datos_sectores[$i][$campos[$j - 1]]/ $totales['cantidad'])*100);
                        break;
                    case 'capital':
                        $tabla = $tabla.'<td>$'.number_format((float)($datos_sectores[$i][$campos[$j]]), 2, ',','.').'</td>';
                        
                        
                        if( $tam_et[$datos_sectores[$i][$campos[1]]] == 'Micro'){
                            $var['xAxis']['categories'][$r] = $datos_sectores[$i][$campos[($j)-3]];
                            $var['series'][0]['data'][$r] = (float)($datos_sectores[$i][$campos[$j]]);
                            $var['series'][0]['name'] = ['Micro'];
                            $r++;
                        }
                        
                        if( $tam_et[$datos_sectores[$i][$campos[1]]] == 'Pequeña'){
                            $var['xAxis']['categories'][$s] = $datos_sectores[$i][$campos[($j)-3]];
                            $var['series'][1]['data'][$s] = (float)($datos_sectores[$i][$campos[$j]]);
                            $var['series'][1]['name'] = ['Pequeña'];
                            $s++;
                        }       
                        if( $tam_et[$datos_sectores[$i][$campos[1]]] == 'Mediana'){
                            $var['xAxis']['categories'][$t] = $datos_sectores[$i][$campos[($j)-3]];
                            $var['series'][2]['data'][$t] = (float)($datos_sectores[$i][$campos[$j]]);
                            $var['series'][2]['name'] = ['Mediana'];
                            $t++;
                        }
                        
                        if( $tam_et[$datos_sectores[$i][$campos[1]]] == 'Grande'){
                            $var['xAxis']['categories'][$u] = $datos_sectores[$i][$campos[($j)-3]];
                            $var['series'][$i]['data'][$u] = (float)($datos_sectores[$i][$campos[$j]]);
                            $var['series'][$i]['name'] = ['Grande'];
                            $u++;
                        }
                        
                        $totales['capital'][$datos_sectores[$i][$campos[$j-2]]] = $totales['capital'][$datos_sectores[$i][$campos[$j-2]]] +$datos_sectores[$i][$campos[$j]];
                        
                        break;
                    
               
                }    
                
                
                
                
            }
            $tabla = $tabla.'</tr>';
            
        }
        $v=1;
        for($v=2;$v <4;$v++){
            $tabla1 = $tabla1.'<tr><th>Totales '.$tam_et[$v].' Empresa:</th><th></th><th></th><th>$'.number_format((float)($totales['capital'][$v]), 2, ',','.').'</th></tr>';
        }
        
        $var['chart']['type'] = 'bar';
        $var['title']['text'] = 'Sectores';
         
        $var['yAxis']['title'] = 'Sectores';
        $customData['tabla1'] = $tabla1;
        $customData['tabla'] = $tabla;
        $customData['url']=$this->module_url;
        
        $return['tabla'] = $this->parser->parse('bonita/bonita_sector_tam_rep_table_view',$customData,true,true);
        $return['grafico'] = $var; 
        echo json_encode($return);
        return $return;
       
    }
    
    
    function bonita_graf_provincia(){ 
    $prueba= Modules::run('bonita/highcharts/bar');   
    
    }
    
    function highcharts($args=array()) {
    	
    	$data['lang'] = $this->lang->language;
    	$data['base_url'] = $this->base_url;
    	$data['module_url'] = $this->module_url;
    	$return['content']=$this->parser->parse('widgets/highcharts', $data, true, true);
    	
    	$return['inlineJS']=<<<BLOCK
    	//------- Highcharts
    	$('#highcharts1').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Fruit Consumption'
        },
        xAxis: {
            categories: ['Apples', 'Bananas', 'Oranges']
        },
        yAxis: {
            title: {
                text: 'Fruit eaten'
            }
        },
        series: [{
            name: 'Jane',
            data: [1, 0, 4]
        }, {
            name: 'John',
            data: [5, 7, 3]
        }]
    });
BLOCK;
    	return $return;
    }
    
    function bonita_reportes_list() {
        
        $model='model_bonita';
        
        $customData = array();
        
        $customData['tabla'] =  '<tr><a href="'.$this->module_url.'" target="_blank">Reportes por Provincia</a></tr></br>'.
                                '<tr><a href="'.$this->module_url.'" target="_blank">Reportes por Provincia</a></tr></br>';
        
        return $this->parser->parse('bonita/bonita_reportes_list_view',$customData,true,true);    
    }
   
    
    function bonita_reporte_provincias_exportar($desde,$hasta){
        
        $model='Model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $provincias = $this->load->model($model)->lista_provincias();
        $prestamos_total = $this->load->model($model)->prestamos_total($desde,$hasta);
        $monto_total = $this->load->model($model)->monto_total($desde,$hasta);
        
        $i = 0;
        $tablatotal = array();
        
        foreach ($provincias as $res){
            $tablatotal[$i] = $this->load->model($model)->montos_provincias($res['nombre'],$prestamos_total,$monto_total,$desde,$hasta);
            $i++;
        }
        
        $campos = array('provincia','prestamos_prov','porcent_prestamos' ,'montos_prov','porcent_montos');
        $tabla = ''; 
        $i = 0;
        $j = 0;
        for($i=0;$i<23;$i++){
            $tabla = $tabla.'<tr>';
            
            for($j=0;$j<5;$j++){
                
                
                if($campos[$j] == 'montos_prov' || $campos[$j] == 'porcent_montos'){
                $tabla = $tabla.'<td>'.number_format((float)($tablatotal[$i][$campos[$j]]), 2, ',','.').'</td>';
                    
                }
                else{
                $tabla = $tabla.'<td>'.$tablatotal[$i][$campos[$j]].'</td>';
                }
            }
            $tabla = $tabla.'</tr>';
        }
        
        $hoy = getdate();
     
        $new_filename = $hoy['year'] . '-';
        if ($hoy['mon'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mon'] . '-';
        else
            $new_filename = $new_filename . $hoy['mon'] . '-';

        if ($hoy['mday'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mday'] . '-';
        else
            $new_filename = $new_filename . $hoy['mday'] . '-';

        if ($hoy['hours'] < 10)
            $new_filename = $new_filename . '0' . $hoy['hours'] . '-';
        else
            $new_filename = $new_filename . $hoy['hours'] . '-';

        if ($hoy['minutes'] < 10)
            $new_filename = $new_filename . '0' . $hoy['minutes'] . '-';
        else
            $new_filename = $new_filename . $hoy['minutes'] . '-';

        if ($hoy['seconds'] < 10)
            $new_filename = $new_filename . '0' . $hoy['seconds'] . '-Bonita-Reporte-Provincias-';
        else
            $new_filename = $new_filename . $hoy['seconds'] . '-Bonita-Reporte-Provincias-';
        
        $new_filename = $new_filename . rand(1000, 5000) .'.xls';
        
        
        
        //$customData['file'] = $table; 
        //$customData['file'] = $table;
        $customData['filename'] = $new_filename; 
        $customData['url'] = $this->module_url;
        $customData['tabla'] = $tabla;
        $default_dashboard = 'bonita/bonita_provincias_rep_table_view_xls';
                
        echo $this->parser->parse($default_dashboard,$customData,true,true);
        
    }

    function bonita_reporte_regiones_exportar($desde,$hasta){
        
        $model='Model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $provincias = $this->load->model($model)->lista_provincias();
        $prestamos_total = $this->load->model($model)->prestamos_total($desde,$hasta);
        $monto_total = $this->load->model($model)->monto_total($desde,$hasta);
        
        $reg = array();
        
        $reg['centro']['nombre'] = 'CENTRO';
        $reg['nea']['nombre'] = 'NEA';
        $reg['noa']['nombre'] = 'NOA';
        $reg['pat']['nombre'] = 'PATAGONIA';
        $reg['cuyo']['nombre'] = 'CUYO';
        $i = 0;
        $j = 0;
        $tablatotal = array();
        $reg_name = array('centro','nea','noa','pat','cuyo');
        $campos = array('nombre','prestamos_prov','montos_prov','cant_norm','monto_norm','relacion');
        for($j=0;$j<5;$j++){
            for($i=1; $i <6; $i++){
                $reg[$reg_name[$j]][$campos[$i]] = 0;
            }
        }
        
        $i = 0;
        foreach ($provincias as $res){
            
            //$tabla = $tabla.'<tr><td>'.$res['nombre'].'</td></tr>';
            $tablatotal[$i] = $this->load->model($model)->montos_provincias($res['nombre'],$prestamos_total,$monto_total,$desde,$hasta);
            
            //CENTRO
            if($tablatotal[$i]['provincia'] == "BUENOS AIRES" || $tablatotal[$i]['provincia']== "CORDOBA" || $tablatotal[$i]['provincia']== "ENTRE RIOS" || $tablatotal[$i]['provincia']== "LA PAMPA" ||  $tablatotal[$i]['provincia']=="SANTA FE" || $tablatotal[$i]['provincia']=="CABA"){
               $reg['centro']['prestamos_prov'] = $reg['centro']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['centro']['porcent_prestamos'] = $reg['centro']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['centro']['montos_prov'] = $reg['centro']['montos_prov'] + $tablatotal[$i]['montos_prov'];
               //$reg['centro']['porcent_montos'] =  $reg['centro']['porcent_montos'] + $tablatotal[$i]['porcent_montos'];
            }
            //NEA
            
            if($tablatotal[$i]['provincia'] == "CHACO" || $tablatotal[$i]['provincia']== "CORRIENTES" || $tablatotal[$i]['provincia']== "FORMOSA" || $tablatotal[$i]['provincia']== "MISIONES"){
               $reg['nea']['prestamos_prov'] = $reg['nea']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['nea']['porcent_prestamos'] = $reg['nea']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['nea']['montos_prov'] = $reg['nea']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              // $reg['nea']['porcent_montos'] = $reg['nea']['porcent_montos'] + $tablatotal[$i]['porcent_montos'];
            }
            //NOA
            if($tablatotal[$i]['provincia'] == "CATAMARCA" || $tablatotal[$i]['provincia']== "JUJUY" || $tablatotal[$i]['provincia']== "LA RIOJA" || $tablatotal[$i]['provincia']== "SALTA" ||  $tablatotal[$i]['provincia']=="SANTIAGO DEL ESTERO" ||  $tablatotal[$i]['provincia']=="TUCUMAN"){
               $reg['noa']['prestamos_prov'] =  $reg['noa']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['noa']['porcent_prestamos'] =  $reg['noa']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['noa']['montos_prov'] =  $reg['noa']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              // $reg['noa']['porcent_montos'] =  $reg['noa']['porcent_montos'] + $tablatotal[$i]['porcent_montos']; 
            }
            //PAT
            if($tablatotal[$i]['provincia'] == "CHUBUT" || $tablatotal[$i]['provincia']== "NEUQUEN" || $tablatotal[$i]['provincia']== "RIO NEGRO" || $tablatotal[$i]['provincia']== "SANTA CRUZ" ||  $tablatotal[$i]['provincia']=="TIERRA DEL FUEGO"){
               $reg['pat']['prestamos_prov'] =  $reg['pat']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              // $reg['pat']['porcent_prestamos'] =  $reg['pat']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
               $reg['pat']['montos_prov'] =  $reg['pat']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              // $reg['pat']['porcent_montos'] =  $reg['pat']['porcent_montos'] + $tablatotal[$i]['porcent_montos']; 
            }
            //CUYO
            if($tablatotal[$i]['provincia'] == "MENDOZA" || $tablatotal[$i]['provincia']== "SAN JUAN" || $tablatotal[$i]['provincia']== "SAN LUIS" ){
                $reg['cuyo']['prestamos_prov'] = $reg['cuyo']['prestamos_prov'] + $tablatotal[$i]['prestamos_prov'];
              //  $reg['cuyo']['porcent_prestamos'] = $reg['cuyo']['porcent_prestamos'] + $tablatotal[$i]['porcent_prestamos'];
                $reg['cuyo']['montos_prov'] = $reg['cuyo']['montos_prov'] + $tablatotal[$i]['montos_prov'];
              //  $reg['cuyo']['porcent_montos'] = $reg['cuyo']['porcent_montos'] + $tablatotal[$i]['porcent_montos']; 
            }
            
            
            
            
            
            
            $i++;
                        
        }
        //var_dump($tablatotal[0][$campos[0]]);
        //exit();
        
        $tabla = ''; 
        $i = 0;
        $j = 0;
        //$tabla = $tabla.'<tr><a href="'.$this->module_url.'poa_report/poa_detail/" target="_blank">Exportar</a></tr>';
        
        $cant_normalizado = array(698160,36088,43453,42634,51129);
        $cant_normalizado_total = 828830;
        $montos_prov_total = 0;
        $prestamos_prov_total = 0;
        $cant_norm_total = 0;
        $monto_norm_total =0;
        $relacion_total = 0;
        $array = array();
        
        // Totales
        
        
        for($i =0; $i<5;$i++){ 
            
           
            for($j=0;$j<6;$j++){
                if($campos[$j] == 'nombre'){
                    
                   
                }
                if($campos[$j] == 'prestamos_prov'){
                    $prestamos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $prestamos_prov_total = $prestamos_prov_total + $prestamos_prov;
                }
                if($campos[$j] == 'montos_prov'){
                    $montos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $montos_prov_total = $montos_prov_total + $montos_prov;
                }
                if($campos[$j] == 'cant_norm'){
                    $cant_norm = $cant_normalizado[$i]/1000;
                    $cant_norm_total = $cant_norm_total + $cant_norm;
                }
                if($campos[$j] == 'monto_norm'){
                    $monto_norm = $reg[$reg_name[$i]][$campos[2]] / $cant_normalizado[$i];
                    $monto_norm_total = $monto_norm_total + $monto_norm;
                    
                } 
            }
            
            
            
        }
        
        // Armo la tabla y el array para el gráfico
        for($i =0; $i<5;$i++){ 
            $tabla = $tabla.'<tr>';
           
            for($j=0;$j<6;$j++){
                if($campos[$j] == 'nombre'){
                    
                    $tabla = $tabla.'<td>'.$reg[$reg_name[$i]][$campos[$j]].'</td>';
                }
                if($campos[$j] == 'prestamos_prov'){
                    $prestamos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $tabla = $tabla.'<td>'.$prestamos_prov.'</td>';
                    //$prestamos_prov_total = $prestamos_prov_total + $prestamos_prov;
                }
                if($campos[$j] == 'montos_prov'){
                    $montos_prov = $reg[$reg_name[$i]][$campos[$j]];
                    $tabla = $tabla.'<td>'.number_format((float)($montos_prov), 2, ',','.').'</td>';
                    //$montos_prov_total = $montos_prov_total + $montos_prov;
                }
                
                if($campos[$j] == 'cant_norm'){
                    $cant_norm = $cant_normalizado[$i]/1000;
                    $tabla = $tabla.'<td>'.number_format((float)($cant_norm), 0, ',','.').'</td>';
                    //$cant_norm_total = $cant_norm_total + $cant_norm;
                }
                
                if($campos[$j] == 'monto_norm'){
                    $monto_norm = $reg[$reg_name[$i]][$campos[2]] / $cant_normalizado[$i];
                    $tabla = $tabla.'<td>'.number_format((float)($monto_norm), 2, ',','.').'</td>';
                    //$monto_norm_total = $monto_norm_total + $monto_norm;
                    
                } 
                
                if($campos[$j] == 'relacion' ){
                    $relacion =  ($monto_norm)/(($montos_prov_total)/($cant_normalizado_total));   
                    $tabla = $tabla.'<td>%'.number_format((float)($relacion), 2, ',','.').'</td>';
                    $relacion_total = $relacion_total +$relacion;
                }
                
            }
            $tabla = $tabla.'</tr>';
              
        }
        $tabla = $tabla.'<tr><th>TOTALES:</th><TH>'.$prestamos_prov_total.'</TH><TH>'.number_format((float)($montos_prov_total), 2, ',','.').
                        '</TH><TH>'.number_format((float)($cant_normalizado_total/1000), 0, ',','.').'</TH><TH>'.number_format((float)($monto_norm_total), 2, ',','.').'</TH>
                        <TH></TH>
                    </tr>';
        
        
        $hoy = getdate();
     
        $new_filename = $hoy['year'] . '-';
        if ($hoy['mon'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mon'] . '-';
        else
            $new_filename = $new_filename . $hoy['mon'] . '-';

        if ($hoy['mday'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mday'] . '-';
        else
            $new_filename = $new_filename . $hoy['mday'] . '-';

        if ($hoy['hours'] < 10)
            $new_filename = $new_filename . '0' . $hoy['hours'] . '-';
        else
            $new_filename = $new_filename . $hoy['hours'] . '-';

        if ($hoy['minutes'] < 10)
            $new_filename = $new_filename . '0' . $hoy['minutes'] . '-';
        else
            $new_filename = $new_filename . $hoy['minutes'] . '-';

        if ($hoy['seconds'] < 10)
            $new_filename = $new_filename . '0' . $hoy['seconds'] . '-Bonita-Reporte-Regiones-';
        else
            $new_filename = $new_filename . $hoy['seconds'] . '-Bonita-Reporte-Regiones-';
        
        $new_filename = $new_filename . rand(1000, 5000) .'.xls';
        
        
        
        //$customData['file'] = $table; 
        //$customData['file'] = $table;
        $customData['filename'] = $new_filename; 
        $customData['url'] = $this->module_url;
        $customData['tabla'] = $tabla;
        $default_dashboard = 'bonita/bonita_regiones_rep_table_view_xls';
                
        echo $this->parser->parse($default_dashboard,$customData,true,true);
        
    }
    
    function bonita_reporte_sectores_exportar($desde,$hasta){
        $model='model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $sectores = $this->load->model($model)->sectores();
        
        
        $totales['capital'] = 0;
        $totales['cantidad'] = 0;
        $totales['porcent_cant'] = 0;
        $totales['porcent_capital'] = 0;
        $z = 0;
        $datos_sectores = array();
        $tabla = ''; 
        foreach($sectores as $sect){
           
            $datos_sectores[$z] = $this->load->model($model)->datos_sectores($sect['sector'],$desde,$hasta);
            $totales['capital'] =  $totales['capital'] + $datos_sectores[$z]['capital'];
            $totales['cantidad'] = $totales['cantidad'] + $datos_sectores[$z]['cantidad'];
            $z++;
        }
        
        $i = 0;
        $j = 0;
        
        $campos =array('sector','cantidad','porcent_cant','capital','porcent_capital');
        for($i=0;$i<$z;$i++){
            $tabla = $tabla.'<tr>';
            
            for($j=0;$j<5;$j++){
                
                
                switch($campos[$j]){
                    case 'sector':
                        $tabla = $tabla.'<td>'.$datos_sectores[$i][$campos[$j]].'</td>';
                        //$var['xAxis']['categories'][$i] = $datos_sectores[$i][$campos[$j]];
                        break;
                    case 'cantidad':
                        $tabla = $tabla.'<td>'.number_format((float)($datos_sectores[$i][$campos[$j]] ), 0, ',','.').'</td>';
                        break;
                    case 'porcent_cant':
                        $tabla = $tabla.'<td>%'.number_format((float)(($datos_sectores[$i][$campos[$j - 1]]/ $totales['cantidad'])*100), 2, ',','.').'</td>';
                        $totales['porcent_cant'] = $totales['porcent_cant'] + (($datos_sectores[$i][$campos[$j - 1]]/ $totales['cantidad'])*100);
                        break;
                    case 'capital':
                        $tabla = $tabla.'<td>$'.number_format((float)($datos_sectores[$i][$campos[$j]]), 2, ',','.').'</td>';
                        //$var['series'][0]['data'][$i] = (float)($datos_sectores[$i][$campos[$j]]);
                        break;
                    case 'porcent_capital':
                        $tabla = $tabla.'<td>%'.number_format((float)((($datos_sectores[$i][$campos[$j -1]] / $totales['capital'])*100)), 2, ',','.').'</td>';
                        $totales['porcent_capital'] =$totales['porcent_capital'] + (($datos_sectores[$i][$campos[$j -1]] / $totales['capital'])*100); 
                        break;
               
                } 
            }
            $tabla = $tabla.'</tr>';
        }      
        
        
        $tabla = $tabla.'<tr><th>Totales:</th><TH>'.number_format((float)($totales['cantidad']), 0, ',','.').'</TH>
        <TH>%'.number_format((float)($totales['porcent_cant']), 2, ',','.').'</TH>
        <TH>$'.number_format((float)($totales['capital']), 0, ',','.').'</TH>
        <TH>%'.number_format((float)($totales['porcent_capital']), 2, ',','.').'</TH></tr>';
        
        
        $hoy = getdate();
     
        $new_filename = $hoy['year'] . '-';
        if ($hoy['mon'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mon'] . '-';
        else
            $new_filename = $new_filename . $hoy['mon'] . '-';

        if ($hoy['mday'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mday'] . '-';
        else
            $new_filename = $new_filename . $hoy['mday'] . '-';

        if ($hoy['hours'] < 10)
            $new_filename = $new_filename . '0' . $hoy['hours'] . '-';
        else
            $new_filename = $new_filename . $hoy['hours'] . '-';

        if ($hoy['minutes'] < 10)
            $new_filename = $new_filename . '0' . $hoy['minutes'] . '-';
        else
            $new_filename = $new_filename . $hoy['minutes'] . '-';

        if ($hoy['seconds'] < 10)
            $new_filename = $new_filename . '0' . $hoy['seconds'] . '-Bonita-Reporte-Sectores-';
        else
            $new_filename = $new_filename . $hoy['seconds'] . '-Bonita-Reporte-Sectores-';
        
        $new_filename = $new_filename . rand(1000, 5000) .'.xls';
        
        
        
        //$customData['file'] = $table; 
        //$customData['file'] = $table;
        $customData['filename'] = $new_filename; 
        $customData['url'] = $this->module_url;
        $customData['tabla'] = $tabla;
        $default_dashboard = 'bonita/bonita_sector_rep_table_view_xls';
                
        echo $this->parser->parse($default_dashboard,$customData,true,true);
       
    }




    function bonita_reporte_sectores_tam_exportar($desde,$hasta){
        
        $model='model_bonita_reportes';
        $customData = array();
        $customData['desde'] = $desde;
        $customData['hasta'] = $hasta;
        $sectores = $this->load->model($model)->sectores();
        $tamanios = $this->load->model($model)->tamanios();
        $z = 0; //sector
        $y = 0; //tamaño
        //$totales['capital'] = array();
        //$totales['capital'][]
        //$totales['cantidad'][$z] = 0;
        //$totales['porcent_cant'] = 0;
        //$totales['porcent_capital'] = 0;
       
        $datos_sectores = array();
        $tabla = '';
        $tabla1 = ''; 
        foreach($sectores as $sect){
            $totales['capital'][$sect['sector']]=0;
           foreach($tamanios as $tams){
                $totales['capital'][$tams['tam_empresa']]=0;
                $datos_sectores[$z] = $this->load->model($model)->datos_sectores_tams($sect['sector'],$tams['tam_empresa'],$desde,$hasta);
            $z++;
           }
            
        }
        
        $i = 0;
        $j = 0;
        $tam_et = array('','Micro','Pequeña','Mediana','Grande');
        $campos = array('sector','tam_empresa','cantidad','capital','porcent_total');
        
        
        for($i=0;$i<$z;$i++){
            $tabla = $tabla.'<tr>';
            
            for($j=0;$j<5;$j++){
                switch($campos[$j]){
                    case 'sector':
                        $tabla = $tabla.'<th>'.$datos_sectores[$i][$campos[$j]].'</th>';
                        //$var['xAxis']['categories'][$i] = $datos_sectores[$i][$campos[$j]];
                        break;
                    case 'tam_empresa':
                        $tabla = $tabla.'<td>'.$tam_et[$datos_sectores[$i][$campos[$j]]].'</td>';
                        break;
                    case 'cantidad':
                        $tabla = $tabla.'<td>'.number_format((float)($datos_sectores[$i][$campos[$j]]), 0, ',','.').'</td>';
                        //$totales['porcent_cant'] = $totales['porcent_cant'] + (($datos_sectores[$i][$campos[$j - 1]]/ $totales['cantidad'])*100);
                        break;
                    case 'capital':
                        $tabla = $tabla.'<td>$'.number_format((float)($datos_sectores[$i][$campos[$j]]), 2, ',','.').'</td>';
                        
                        $totales['capital'][$datos_sectores[$i][$campos[$j-2]]] = $totales['capital'][$datos_sectores[$i][$campos[$j-2]]] +$datos_sectores[$i][$campos[$j]];
                        
                        break;
                    
               
                }    
                
                
                
                
            }
            $tabla = $tabla.'</tr>';
            
        }
        $v=1;
        for($v=2;$v <4;$v++){
            $tabla1 = $tabla1.'<tr><th>Totales '.$tam_et[$v].' Empresa:</th><th></th><th></th><th>$'.number_format((float)($totales['capital'][$v]), 2, ',','.').'</th></tr>';
        }
        
       
        $hoy = getdate();
     
        $new_filename = $hoy['year'] . '-';
        if ($hoy['mon'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mon'] . '-';
        else
            $new_filename = $new_filename . $hoy['mon'] . '-';

        if ($hoy['mday'] < 10)
            $new_filename = $new_filename . '0' . $hoy['mday'] . '-';
        else
            $new_filename = $new_filename . $hoy['mday'] . '-';

        if ($hoy['hours'] < 10)
            $new_filename = $new_filename . '0' . $hoy['hours'] . '-';
        else
            $new_filename = $new_filename . $hoy['hours'] . '-';

        if ($hoy['minutes'] < 10)
            $new_filename = $new_filename . '0' . $hoy['minutes'] . '-';
        else
            $new_filename = $new_filename . $hoy['minutes'] . '-';

        if ($hoy['seconds'] < 10)
            $new_filename = $new_filename . '0' . $hoy['seconds'] . '-Bonita-Reporte-Sectores-Tamaño-';
        else
            $new_filename = $new_filename . $hoy['seconds'] . '-Bonita-Reporte-Sectores-Tamaño-';
        
        $new_filename = $new_filename . rand(1000, 5000) .'.xls';
        
        
        
        $customData['filename'] = $new_filename; 
        $customData['url'] = $this->module_url;
        $customData['tabla'] = $tabla;
        $customData['tabla1'] = $tabla1;
        $default_dashboard = 'bonita/bonita_sector_tam_rep_table_view_xls';
                
        echo $this->parser->parse($default_dashboard,$customData,true,true);
    }    
    */
}
