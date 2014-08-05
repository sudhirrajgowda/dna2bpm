<?php

class Lib_14_data extends MX_Controller {
    /* VALIDADOR ANEXO 14 */

    public function __construct($parameter) {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('sgr/tools');
        $this->load->model('sgr/sgr_model');

        $model_12 = 'model_12';
        $this->load->Model($model_12);

        $model_anexo = "model_14";
        $this->load->Model($model_anexo);



        /* Vars 
         * 
         * $parameters =  
         * $parameterArr[0]['fieldValue'] 
         * $parameterArr[0]['row'] 
         * $parameterArr[0]['col']
         * $parameterArr[0]['count']
         * 
         */
        $stack = array();
        $original_array = array();
        $parameterArr = (array) $parameter;
        $result = array("error_code" => "", "error_row" => "", "error_input_value" => "");
        $this->$model_anexo->clear_tmp($insert_tmp);

        $order_num = array();





        for ($i = 1; $i <= $parameterArr[0]['count']; $i++) {
            /**
             * BASIC VALIDATION
             * 
             * @param 
             * @type PHP
             * @name ...
             * @author Diego             
             * @example 
             * FECHA_MOVIMIENTO	NRO_GARANTIA	CAIDA	RECUPERO	INCOBRABLES_PERIODO	GASTOS_EFECTUADOS_PERIODO	RECUPERO_GASTOS_PERIODO	GASTOS_INCOBRABLES_PERIODO
             * */
            for ($i = 0; $i <= count($parameterArr); $i++) {




                /* FECHA_MOVIMIENTO
                 * Nro A.1
                 * Detail:
                 * Debe tener formato numérico de hasta 5 dígitos.
                 * Nro A.2
                 * Detail:
                 * La fecha debe estar dentro del período informado.
                 */

                if ($parameterArr[$i]['col'] == 1) {

                    $sum_cdefgh = array();

                    $A_cell_value = "";
                    $code_error = "A.1";
                    
                    //empty field Validation
                    $return = check_empty($parameterArr[$i]['fieldValue']);
                    if ($return) {
                        $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                        array_push($stack, $result);
                    } else {
                        $A_cell_value = $parameterArr[$i]['fieldValue'];
                        $return = check_date_format($parameterArr[$i]['fieldValue']);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }


                        /* PERIOD */
                        $return = check_period($parameterArr[$i]['fieldValue'], $this->session->userdata['period']);
                        if ($return) {
                            $code_error = "A.2";
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }
                    }
                }

                /* NRO_GARANTIA
                 * Nro B.1
                 * Detail:
                 * Si se está informando la CAÍDA de una Garantía (Columna C del importador), 
                 * debe validar que el número de garantía se encuentre registrado en el Sistema como que fue otorgada (Anexo 12).                
                 */

                if ($parameterArr[$i]['col'] == 2) {
                    $B_cell_value = $parameterArr[$i]['fieldValue'];
                    $order_num[] = $B_cell_value;
                    /* WARRANTY DATA */
                    $B_warranty_info = $this->$model_12->get_order_number_left($parameterArr[$i]['fieldValue']);
                }

                /* CAIDA
                 * Nro C.1
                 * Detail:
                 * Formato de número. Debe ser un valor numérico y aceptar hasta 2 decimales.
                 */
                if ($parameterArr[$i]['col'] == 3) {

                    $code_error = "C.1";
                    if ($parameterArr[$i]['fieldValue'] != "") {

                        $sum_cdefgh[] = 1;


                        $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }


                        /* Nro C.2.A
                         * Detail:
                         * En caso de que la garantía haya sido otorgada en PESOS, debe validar que el importe sea menor o igual al 
                         * Monto de la Garantía Otorgada informada mediante Anexo 12 registrado en el Sistema. 
                         */

                        /* MONEDA 5219 | IMPORTE 5218 */


                        foreach ($B_warranty_info as $c_info) {

                            $C_cell_value = (float) $parameterArr[$i]['fieldValue'];

                            /* Nro C.3
                             * Detail:
                             * En  caso de que la garantía haya sido otorgada en DÓLARES debe validar que el importe aquí informado sea menor o igual 
                             * al Monto de la Garantía Otorgada informado mediante Anexo 12 registrado en el Sistema, dividido por el 
                             * TIPO DE CAMBIO DEL día anterior al que fue otorgada la garantía y multiplicado por el TIPO DE CAMBIO del día 
                             * anterior al que se está informando que se cayó la garantía. */

                            if ($c_info['5219'][0] == 2) {

                                $dollar_quotation_origin = $this->sgr_model->get_dollar_quotation(translate_date_xls($c_info['5215']));

                                $dollar_quotation = $this->sgr_model->get_dollar_quotation($A_cell_value);
                                $dollar_value = ($c_info[5218] / $dollar_quotation_origin) * $dollar_quotation;


                                /* FIX */
                                $fix_ten_cents = fix_ten_cents($dollar_value, $C_cell_value);
                                if ($fix_ten_cents) {
                                    $code_error = "C.3";
                                    $result = return_error_array($code_error, $parameterArr[$i]['row'], money_format_custom($C_cell_value) . ' Monto disponible para el Nro. Orden  = ' . $B_cell_value . '  (' . money_format_custom($c_info[5218]) . '/' . money_format_custom($dollar_quotation_origin) . '*' . money_format_custom($dollar_quotation) . ' = ' . money_format_custom($dollar_value) . ' )');
                                    array_push($stack, $result);
                                }

                                $dollar_quotation_period = $this->sgr_model->get_dollar_quotation_period();
                                $new_dollar_value = ($c_info[5218] / $dollar_quotation_origin) * $dollar_quotation_period;

                                //Ejemplo “($ 100.000.000). Monto disponible para el Nro. Orden 49720 = $900000/4.878*8.018 =1.479.335.7933”

                                /* FIX */
                                $a = (int) $new_dollar_value;
                                $b = (int) $C_cell_value;

                                $fix_ten_cents = fix_ten_cents($a, $b);

//                                if ($fix_ten_cents) {
//                                    $code_error = "C.2.B";
//                                    $result = return_error_array($code_error, $parameterArr[$i]['row'], money_format_custom($C_cell_value) . ' Monto disponible para el Nro. Orden  ' . $B_cell_value . ' =  (' . money_format_custom($c_info[5218]) . '/' . money_format_custom($dollar_quotation_origin) . '*' . money_format_custom($dollar_quotation_period) . ' = ' . money_format_custom($new_dollar_value) . ')');
//                                    array_push($stack, $result);
//                                }
                            } else {
                                if ($C_cell_value > $c_info[5218]) {
                                    $code_error = "C.2.A";
                                    $result = return_error_array($code_error, $parameterArr[$i]['row'], '(' . money_format_custom($C_cell_value) . '). Monto disponible para el Nro. Orden ' . $B_cell_value . ' = ' . money_format_custom($c_info[5218]));
                                    array_push($stack, $result);
                                }
                            }
                        }


                        /* Nro B.1
                         * Detail:
                         * Si se está informando la CAÍDA de una Garantía (Columna C del importador), 
                         * debe validar que el número de garantía se encuentre registrado en el Sistema como que fue otorgada (Anexo 12). 
                         */
                        if (!$B_warranty_info) {
                            $code_error = "B.1";
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $B_cell_value);
                            array_push($stack, $result);
                        }

                        /* INSERT */
                        $insert_tmp = array();
                        $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                        $insert_tmp['NRO_GARANTIA'] = $B_cell_value;
                        $insert_tmp['CAIDA'] = $parameterArr[$i]['fieldValue'];

                        $this->$model_anexo->save_tmp($insert_tmp);
                    }
                }

                /* RECUPERO
                 * Nro D.1
                 * Detail:
                 * Formato de número. Debe ser un valor numérico y aceptar hasta 2 decimales.
                 * Nro D.2
                 * Detail:
                 * Debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) una caída.                 
                 */
                if ($parameterArr[$i]['col'] == 4) {

                    $code_error = "D.1";
                    if ($parameterArr[$i]['fieldValue'] != "") {

                        $sum_cdefgh[] = 1;

                        $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }

                        /* INSERT */
                        $insert_tmp = array();
                        $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                        $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                        $insert_tmp['RECUPERO'] = $parameterArr[$i]['fieldValue'];

                        $this->$model_anexo->save_tmp($insert_tmp);
                    }
                }

                /* INCOBRABLES_PERIODO
                 * Nro E.1
                 * Detail:
                 * Formato de número. Debe ser un valor numérico y aceptar hasta 2 decimales.
                 * Nro E.2
                 * Detail:
                 * Debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) una caída.                
                 */
                if ($parameterArr[$i]['col'] == 5) {

                    $code_error = "E.1";
                    if ($parameterArr[$i]['fieldValue'] != "") {

                        $sum_cdefgh[] = 1;

                        $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }

                        /* INSERT */
                        $insert_tmp = array();
                        $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                        $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                        $insert_tmp['INCOBRABLES_PERIODO'] = $parameterArr[$i]['fieldValue'];

                        $this->$model_anexo->save_tmp($insert_tmp);
                    }
                }

                /* GASTOS_EFECTUADOS_PERIODO
                 * Nro F.1
                 * Detail:
                 * Formato de número. Debe ser un valor numérico y aceptar hasta 2 decimales.
                 * Nro F.2
                 * Detail:
                 * Debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) una caída.
                 */
                if ($parameterArr[$i]['col'] == 6) {
                    $code_error = "F.1";
                    if ($parameterArr[$i]['fieldValue'] != "") {

                        $sum_cdefgh[] = 1;

                        $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }

                        /* INSERT */
                        $insert_tmp = array();
                        $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                        $insert_tmp['NRO_GARANTIA'] = $B_cell_value;
                        $insert_tmp['GASTOS_EFECTUADOS_PERIODO'] = $parameterArr[$i]['fieldValue'];

                        $this->$model_anexo->save_tmp($insert_tmp);
                    }
                }

                /* RECUPERO_GASTOS_PERIODO
                 * G.1
                  Debe ser un valor numérico y aceptar hasta 2 decimales.
                  G.2 = B.5
                  Debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) un GASTO POR GESTIÓN DE RECUPERO.
                  G.3
                  Debe validar que la suma de todos los RECUPEROS POR GASTOS DE GESTIÓN DE RECUPEROS e
                 * INCOBRABLES POR GASTOS DE GESTIÓN DE RECUPEROS registrados en el Sistema 
                 * (incluidos los informados  en el archivo que se está importando) para una misma garantía no supere la suma de todos los 
                 * GASTOS POR GESTIÓN DE RECUPEROS de esa misma garantía registrados en el Sistema (incluidos los informados  en el archivo que 
                 * se está importando).
                 */
                if ($parameterArr[$i]['col'] == 7) {
                    $code_error = "G.1";
                    if ($parameterArr[$i]['fieldValue'] != "") {
                        $sum_cdefgh[] = 1;

                        $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }

                        /* INSERT */
                        $insert_tmp = array();
                        $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                        $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                        $insert_tmp['RECUPERO_GASTOS_PERIODO'] = $parameterArr[$i]['fieldValue'];

                        $this->$model_anexo->save_tmp($insert_tmp);
                    }
                }

                /* GASTOS_INCOBRABLES_PERIODO 
                  H.1
                  Debe ser un valor numérico y aceptar hasta 2 decimales.
                  H.2 = B.6
                  Debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) un GASTO POR GESTIÓN DE RECUPERO.
                  H.3 = G.3
                  Debe validar que la suma de todos los RECUPEROS POR GASTOS DE GESTIÓN DE RECUPEROS e INCOBRABLES POR GASTOS DE GESTIÓN DE RECUPEROS registrados en el Sistema (incluidos los informados  en el archivo que se está importando) para una misma garantía no supere la suma de todos los GASTOS POR GESTIÓN DE RECUPEROS de esa misma garantía registrados en el Sistema (incluidos los informados  en el archivo que se está importando).
                 */
                if ($parameterArr[$i]['col'] == 8) {
                    $code_error = "H.1";
                    if ($parameterArr[$i]['fieldValue'] != "") {
                        $sum_cdefgh[] = 1;

                        $return = check_decimal($parameterArr[$i]['fieldValue']);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }

                        /* INSERT */
                        $insert_tmp = array();
                        $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                        $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                        $insert_tmp['GASTOS_INCOBRABLES_PERIODO'] = $parameterArr[$i]['fieldValue'];
                        $this->$model_anexo->save_tmp($insert_tmp);
                    }

                    /* VG.1 */
                    $VG1 = array_sum($sum_cdefgh);
                    if ($VG1 > 1) {
                        $code_error = "VG.1";
                        $result = return_error_array($code_error, $parameterArr[$i]['row'], "");
                        array_push($stack, $result);
                    }
                }
            } // END FOR LOOP->
        }




        $order_num_unique = array_unique($order_num);

        foreach ($order_num_unique as $number) {




            /* MOVEMENT DATA */
            $get_historic_data = $this->$model_anexo->get_movement_data($number);
            $get_temp_data = $this->$model_anexo->get_tmp_movement_data($number);

            $sum_CAIDA = array_sum(array($get_historic_data['CAIDA'], $get_temp_data['CAIDA']));
            $sum_RECUPERO = array_sum(array($get_historic_data['RECUPERO'], $get_temp_data['RECUPERO']));
            $sum_INCOBRABLES_PERIODO = array_sum(array($get_historic_data['INCOBRABLES_PERIODO'], $get_temp_data['INCOBRABLES_PERIODO']));
            $sum_RECUPEROS = array_sum(array($sum_RECUPERO, $sum_INCOBRABLES_PERIODO));

            $sum_GASTOS_EFECTUADOS_PERIODO = array_sum(array($get_historic_data['GASTOS_EFECTUADOS_PERIODO'], $get_temp_data['GASTOS_EFECTUADOS_PERIODO']));
            $sum_RECUPERO_GASTOS_PERIODO = array_sum(array($get_historic_data['RECUPERO_GASTOS_PERIODO'], $get_temp_data['RECUPERO_GASTOS_PERIODO']));
            $sum_GASTOS_INCOBRABLES_PERIODO = array_sum(array($get_historic_data['GASTOS_INCOBRABLES_PERIODO'], $get_temp_data['GASTOS_INCOBRABLES_PERIODO']));
            $sum_GASTOS = array_sum(array($sum_RECUPERO_GASTOS_PERIODO, $sum_GASTOS_INCOBRABLES_PERIODO));






            /* Nro B.2/D.2
             * Detail:
             * Si se está informando un RECUPERO (Columna D del importador), debe validar que el número de garantía registre 
             * previamente en el sistema (o en el mismo archivo que se está importando) una caída. 
             */

            if ($get_temp_data['INCOBRABLES_PERIODO']) {
               
                if ($sum_CAIDA < $sum_RECUPEROS) {
                    $code_error = "E.3";
                    $result = return_error_array($code_error, "", "[" . $get_temp_data['INCOBRABLES_PERIODO'] . "] saldo de caidas " . $sum_CAIDA);
                    array_push($stack, $result);
                }
            }

            if ($get_temp_data['RECUPERO'] > 0) {
                if ($sum_CAIDA == 0) {
                    $code_error = "B.2";
                    $result = return_error_array($code_error, "", $get_temp_data['RECUPERO']);
                    array_push($stack, $result);
                }

                /* D.3 */
                if ($sum_RECUPEROS > $sum_CAIDA) {
                    $code_error = "D.3";
                    $result = return_error_array($code_error, "", "( Nro de Orden " . $number . " Caidas: " . $sum_CAIDA . " ) " . $get_historic_data['RECUPERO'] . "/" . $get_temp_data['RECUPERO'] . "+" . $get_historic_data['INCOBRABLES_PERIODO'] . "/" . $get_temp_data['INCOBRABLES_PERIODO']);
                    array_push($stack, $result);
                }


                /* D.3 */
                $query_param = 'RECUPERO';
                $get_recuperos_tmp = $this->$model_anexo->get_recuperos_tmp($number, $query_param);
                foreach ($get_recuperos_tmp as $recuperos) {
                    $caidas = $this->$model_anexo->get_caida_tmp($number, $recuperos);
                    $return_calc = calc_anexo_14($caidas, $get_historic_data, $number);
                    if ($return_calc) {
                        $code_error = "D.3";
                        $result = return_error_array($code_error, "", "[" . $query_param . "] " . $return_calc);
                        array_push($stack, $result);
                    }
                }

                $query_param = 'INCOBRABLES_PERIODO';
                $get_recuperos_tmp = $this->$model_anexo->get_recuperos_tmp($number, $query_param);




                foreach ($get_recuperos_tmp as $recuperos) {
                    $caidas = $this->$model_anexo->get_caida_tmp($number, $recuperos);
                    $return_calc = calc_anexo_14($caidas, $get_historic_data, $number);
                    if ($return_calc) {
                        $code_error = "E.3";
                        $result = return_error_array($code_error, "", "[" . $query_param . "] " . $return_calc);
                        array_push($stack, $result);
                    }
                }
            }


            /* Nro B.3
             * Detail:
             * Si se está informando un INCOBRABLE (Columna E del importador), debe validar que el número de garantía registre 
             * previamente en el sistema (o en el mismo archivo que se está importando) una caída. 
             */
            if ($get_temp_data['INCOBRABLES_PERIODO'] > 0) {
                if ($sum_CAIDA == 0) {
                    $code_error = "B.3";
                    $result = return_error_array($code_error, "", $get_temp_data['INCOBRABLES_PERIODO']);
                    array_push($stack, $result);
                }
            }

            /* Nro B.4
             * Detail: 
             * Si se está informando un GASTOS POR GESTIÓN DE RECUPERO (Columna F), 
             * debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) una caída.
             */
            if ($get_temp_data['GASTOS_EFECTUADOS_PERIODO'] > 0) {
                if ($sum_CAIDA == 0) {
                    $code_error = "B.4";
                    $result = return_error_array($code_error, "", $get_temp_data['GASTOS_EFECTUADOS_PERIODO']);
                    array_push($stack, $result);
                }
            }

            /* Nro B.5
             * Detail: 
             * Si se está informando un RECUPERO DE GASTOS POR GESTIÓN DE RECUPERO (Columna G), 
             * debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) un 
             * GASTO POR GESTIÓN DE RECUPERO. 
             */
            if ($get_temp_data['RECUPERO_GASTOS_PERIODO'] > 0) {
                if ($sum_RECUPERO_GASTOS_PERIODO == 0) {
                    $code_error = "B.5";
                    $result = return_error_array($code_error, "", $get_temp_data['GASTOS_EFECTUADOS_PERIODO']);
                    array_push($stack, $result);
                }

                /* G.3 */

                if ($sum_GASTOS > $sum_GASTOS_EFECTUADOS_PERIODO) {
                    $code_error = "G.3";
                    $result = return_error_array($code_error, "", "( Nro de Orden " . $number . " Gastos Efectuados: " . $sum_GASTOS_EFECTUADOS_PERIODO . " ) " . $get_historic_data['RECUPERO_GASTOS_PERIODO'] . "/" . $get_temp_data['RECUPERO_GASTOS_PERIODO'] . "+" . $get_historic_data['GASTOS_INCOBRABLES_PERIODO'] . "/" . $get_temp_data['GASTOS_INCOBRABLES_PERIODO']);
                    array_push($stack, $result);
                }


                $query_param = 'RECUPERO_GASTOS_PERIODO';
                $get_gastos_tmp = $this->$model_anexo->get_gastos_tmp($number, $query_param);
                foreach ($get_gastos_tmp as $gastos) {
                    $gastos = $this->$model_anexo->get_gastos_tmp($number, $gastos);
                    $return_calc = calc_anexo_14_gastos($gastos, $get_historic_data, $number);
                    if ($return_calc) {
                        $code_error = "G.3";
                        $result = return_error_array($code_error, "", "[" . $query_param . "] " . $return_calc);
                        array_push($stack, $result);
                    }
                }

                $query_param = 'GASTOS_INCOBRABLES_PERIODO';
                $get_gastos_tmp = $this->$model_anexo->get_gastos_tmp($number, $query_param);
                foreach ($get_gastos_tmp as $gastos) {
                    $gastos = $this->$model_anexo->get_gastos_tmp($number, $gastos);
                    $return_calc = calc_anexo_14_gastos($gastos, $get_historic_data, $number);
                    if ($return_calc) {
                        $code_error = "G.3";
                        $result = return_error_array($code_error, "", "[" . $query_param . "] " . $return_calc);
                        array_push($stack, $result);
                    }
                }
            }

            /* Nro B.6
             * Detail: 
             * Si se está informando un INCOBRABLE DE GASTOS POR GESTIÓN DE RECUPERO (Columna G), 
             * debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) un 
             * GASTO POR GESTIÓN DE RECUPERO. 
             */
            if ($get_temp_data['RECUPERO_GASTOS_PERIODO'] > 0) {
                if ($sum_RECUPERO_GASTOS_PERIODO == 0) {
                    $code_error = "B.6";
                    $result = return_error_array($code_error, "", $get_temp_data['GASTOS_INCOBRABLES_PERIODO']);
                    array_push($stack, $result);
                }
            }
        }
        //var_dump($stack);        exit();
        $this->data = $stack;
    }

}