<?php

class Lib_14_data extends MX_Controller {
    /* VALIDADOR ANEXO 14 */

    public function __construct($parameter) {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('sgr/tools');
        $this->load->model('sgr/sgr_model');
        $this->load->Model('model_12');
        $this->load->Model('model_14');

        /* Vars 
         * 
         * $parameters =  
         * $parameterArr[0]['fieldValue'] 
         * $parameterArr[0]['row'] 
         * $parameterArr[0]['col']
         * $parameterArr[0]['count']
         * 
         */
        $result = array();
        $parameterArr = (array) $parameter;

        $insert_tmp = array();
        $this->model_14->clear_tmp($insert_tmp);

        $order_num = array();
        $C_arr = array();
        $D_arr = array();
        $E_arr = array();
        $F_arr = array();
        $G_arr = array();
        $H_arr = array();
        


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


            $param_col = (isset($parameterArr[$i]['col'])) ? $parameterArr[$i]['col'] : 0;


            /* FECHA_MOVIMIENTO
             * Nro A.1
             * Detail:
             * Debe tener formato numérico de hasta 5 dígitos.
             * Nro A.2
             * Detail:
             * La fecha debe estar dentro del período informado.
             */

            if ($param_col == 1) {

                $sum_cdefgh = array();

                $A_cell_value = "";
                $code_error = "A.1";
                $A_cell_value = $parameterArr[$i]['fieldValue'];

                if (empty($parameterArr[$i]['fieldValue'])) {
                    $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                } else {
                    $return = check_date_format($parameterArr[$i]['fieldValue']);
                    if ($return) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }
                    /* PERIOD */
                    $return = check_period($parameterArr[$i]['fieldValue'], $this->session->userdata['period']);
                    if ($return) {
                        $code_error = "A.2";
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }
                }
            }

            /* NRO_GARANTIA
             * Nro B.1
             * Detail:
             * Si se está informando la CAÍDA de una Garantía (Columna C del importador), 
             * debe validar que el número de garantía se encuentre registrado en el Sistema como que fue otorgada (Anexo 12).                
             */

            if ($param_col == 2) {

                $B_cell_value = $parameterArr[$i]['fieldValue'];

                $id_array = $parameterArr[$i]['row'] . $A_cell_value . $B_cell_value;

                $order_num[$id_array]['row'] = $parameterArr[$i]['row'];
                $order_num[$id_array]['date'] = $A_cell_value;
                $order_num[$id_array]['warranty'] = $B_cell_value;


                $return = warranty_number_checker($parameterArr[$i]['fieldValue']);
                if ($return) {
                    $code_error = "B.7";
                    $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                } else {
                    /* WARRANTY DATA */
                    $B_warranty_info = $this->model_12->get_order_number_left($parameterArr[$i]['fieldValue']);
                }
            }

            /* CAIDA
             * Nro C.1
             * Detail:
             * Formato de número. Debe ser un valor numérico y aceptar hasta 2 decimales.
             */
            if ($param_col == 3) {

                $code_error = "C.1";
                if ($parameterArr[$i]['fieldValue'] != "") {

                    $sum_cdefgh[] = 1;
                    $C_array_values = $A_cell_value . $B_cell_value;

                    $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                    if ($return) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }

                    /* V.C.1.A array */
                    if (in_array($C_array_values, $C_arr)) {
                        $code_error = "C.1.A";
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $A_cell_value . " - " . $B_cell_value);
                    }
                    $C_arr[] = $C_array_values;


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

                            /* SOLO SI LA CAIDO ES MAYOR AL IMPORTE DE LA GARANTIA */
                            if ($dollar_value < $C_cell_value) {
                                /* FIX */
                                $fix_ten_cents = fix_ten_cents($dollar_value, $C_cell_value);
                                if ($fix_ten_cents) {
                                    $code_error = "C.3";
                                    $result[] = return_error_array($code_error, $parameterArr[$i]['row'], money_format_custom($C_cell_value) . ' Monto disponible para el Nro. Orden  = ' . $B_cell_value . '  (' . money_format_custom($c_info[5218]) . '/' . money_format_custom($dollar_quotation_origin) . '*' . money_format_custom($dollar_quotation) . ' = ' . money_format_custom($dollar_value) . ' )');
                                }
                            }




                            $dollar_quotation_period = $this->sgr_model->get_dollar_quotation_period();
                            $new_dollar_value = ($c_info[5218] / $dollar_quotation_origin) * $dollar_quotation_period;

                            //Ejemplo “($ 100.000.000). Monto disponible para el Nro. Orden 49720 = $900000/4.878*8.018 =1.479.335.7933”

                            /* FIX */
                            $a = (int) $new_dollar_value;
                            $b = (int) $C_cell_value;

                            $fix_ten_cents = fix_ten_cents($a, $b);
                            if ($fix_ten_cents) {
                                $code_error = "C.2.B";
                                $result[] = return_error_array($code_error, $parameterArr[$i]['row'], money_format_custom($C_cell_value) . ' Monto disponible para el Nro. Orden  ' . $B_cell_value . ' =  (' . money_format_custom($c_info[5218]) . '/' . money_format_custom($dollar_quotation_origin) . '*' . money_format_custom($dollar_quotation_period) . ' = ' . money_format_custom($new_dollar_value) . ')');
                            }
                        } else {
                            if ($C_cell_value > $c_info[5218]) {
                                $code_error = "C.2.A";
                                $result[] = return_error_array($code_error, $parameterArr[$i]['row'], '(' . money_format_custom($C_cell_value) . '). Monto disponible para el Nro. Orden ' . $B_cell_value . ' = ' . money_format_custom($c_info[5218]));
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
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $B_cell_value);
                    }

                    /* INSERT */
                    $insert_tmp = array();
                    $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                    $insert_tmp['NRO_GARANTIA'] = $B_cell_value;
                    $insert_tmp['CAIDA'] = $parameterArr[$i]['fieldValue'];
                    $insert_tmp['ID'] = $parameterArr[$i]['row'];

                    $this->model_14->save_tmp($insert_tmp);
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
            if ($param_col == 4) {

                $code_error = "D.1";
                if ($parameterArr[$i]['fieldValue'] != "") {

                    $sum_cdefgh[] = 1;

                    $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                    if ($return) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }

                    /* INSERT */
                    $insert_tmp = array();
                    $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                    $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                    $insert_tmp['RECUPERO'] = $parameterArr[$i]['fieldValue'];
                    $insert_tmp['ID'] = $parameterArr[$i]['row'];

                    $this->model_14->save_tmp($insert_tmp);


                    /* V.D.1.A array */
                    $D_array_values = $A_cell_value . $B_cell_value;
                    
                    if (in_array($D_array_values, $D_arr)) {
                        $code_error = "D.1.A";
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $A_cell_value . " - " . $B_cell_value);
                    }
                    $D_arr[] = $D_array_values;
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
            if ($param_col == 5) {

                $code_error = "E.1";
                if ($parameterArr[$i]['fieldValue'] != "") {

                    $sum_cdefgh[] = 1;

                    $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                    if ($return) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }

                    /* INSERT */
                    $insert_tmp = array();
                    $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                    $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                    $insert_tmp['INCOBRABLES_PERIODO'] = $parameterArr[$i]['fieldValue'];
                    $insert_tmp['ID'] = $parameterArr[$i]['row'];
                    $this->model_14->save_tmp($insert_tmp);
                    
                    
                    /* V.E.1.A array */
                    $E_array_values = $A_cell_value . $B_cell_value;
                    
                    if (in_array($E_array_values, $E_arr)) {
                        $code_error = "E.1.A";
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $A_cell_value . " - " . $B_cell_value);
                    }
                    $E_arr[] = $E_array_values;
                    
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
            if ($param_col == 6) {
                $code_error = "F.1";
                if (!empty($parameterArr[$i]['fieldValue'])) {

                    $sum_cdefgh[] = 1;

                    $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                    if ($return) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }

                    /* INSERT */
                    $insert_tmp = array();
                    $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                    $insert_tmp['NRO_GARANTIA'] = $B_cell_value;
                    $insert_tmp['GASTOS_EFECTUADOS_PERIODO'] = $parameterArr[$i]['fieldValue'];
                    $insert_tmp['ID'] = $parameterArr[$i]['row'];
                    $this->model_14->save_tmp($insert_tmp);
                    
                    /* V.F.1.A array */
                    $F_array_values = $A_cell_value . $B_cell_value;
                    
                    if (in_array($F_array_values, $F_arr)) {
                        $code_error = "F.1.A";
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $A_cell_value . " - " . $B_cell_value);
                    }
                    $F_arr[] = $F_array_values;
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
            if ($param_col == 7) {
                $code_error = "G.1";
                if ($parameterArr[$i]['fieldValue'] != "") {
                    $sum_cdefgh[] = 1;

                    $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                    if ($return) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }

                    /* INSERT */
                    $insert_tmp = array();
                    $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                    $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                    $insert_tmp['RECUPERO_GASTOS_PERIODO'] = $parameterArr[$i]['fieldValue'];
                    $insert_tmp['ID'] = $parameterArr[$i]['row'];
                    $this->model_14->save_tmp($insert_tmp);
                    
                    /* V.G.1.A array */
                    $G_array_values = $A_cell_value . $B_cell_value;
                    
                    if (in_array($G_array_values, $G_arr)) {
                        $code_error = "G.1.A";
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $A_cell_value . " - " . $B_cell_value);
                    }
                    $G_arr[] = $G_array_values;
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
            if ($param_col == 8) {
                $code_error = "H.1";
                if ($parameterArr[$i]['fieldValue'] != "") {
                    $sum_cdefgh[] = 1;

                    $return = check_decimal($parameterArr[$i]['fieldValue']);
                    if ($return) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }

                    /* INSERT */
                    $insert_tmp = array();
                    $insert_tmp['FECHA_MOVIMIENTO'] = $A_cell_value;
                    $insert_tmp['NRO_GARANTIA'] = $B_cell_value;

                    $insert_tmp['GASTOS_INCOBRABLES_PERIODO'] = $parameterArr[$i]['fieldValue'];
                    $insert_tmp['ID'] = $parameterArr[$i]['row'];
                    $this->model_14->save_tmp($insert_tmp);
                    
                    /* V.H.1.A array */
                    $H_array_values = $A_cell_value . $B_cell_value;
                    
                    if (in_array($H_array_values, $H_arr)) {
                        $code_error = "H.1.A";
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $A_cell_value . " - " . $B_cell_value);
                    }
                    $H_arr[] = $H_array_values;
                }

                /* VG.1 */
                $VG1 = array_sum($sum_cdefgh);
                if ($VG1 > 1) {
                    $code_error = "VG.1";
                    $result[] = return_error_array($code_error, $parameterArr[$i]['row'], "");
                }
            }
        } // END FOR LOOP->       





        foreach ($order_num as $filter) {

            $sum_CAIDA = 0;
            $sum_RECUPERO = 0;
            $sum_INCOBRABLES_PERIODO = 0;
            $sum_RECUPEROS = 0;

            $sum_GASTOS_EFECTUADOS_PERIODO = 0;
            $sum_RECUPERO_GASTOS_PERIODO = 0;
            $sum_GASTOS_INCOBRABLES_PERIODO = 0;
            $sum_GASTOS = 0;


            $get_temp_data = null;
            /* MOVEMENT DATA */
            $get_historic_data = $this->model_14->get_movement_data($filter);
            $get_temp_data = $this->model_14->get_tmp_movement_data($filter);


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

                /* CAIDAS + RECUPEROS???? < $get_temp_data['INCOBRABLES_PERIODO'] */

                $recuperos_plus_incobrable = array_sum($sum_RECUPEROS, $get_temp_data['INCOBRABLES_PERIODO']);

                $get_recuperos_plus_incobrable = bccomp($sum_CAIDA, $get_temp_data['INCOBRABLES_PERIODO']);
                if ($get_temp_data_RECUPEROS == -1) {
                    $code_error = "E.3";
                    $result[] = return_error_array($code_error, $filter['row'], "[" . $get_temp_data['INCOBRABLES_PERIODO'] . "] saldo de caidas " . $sum_CAIDA);
                }
            }


            $get_temp_data_RECUPERO = bccomp($get_temp_data['RECUPERO'], 0);
            if ($get_temp_data_RECUPERO == 1) {
                if ($sum_CAIDA == 0) {
                    $code_error = "B.2";
                    $result[] = return_error_array($code_error, $filter['row'], $get_temp_data['RECUPERO']);
                }
            }


            /* D.3 */

//            $get_temp_data_RECUPEROS = bccomp($sum_RECUPEROS, $sum_CAIDA);
//            if ($get_temp_data_RECUPEROS == 1) {
//                $code_error = "D.3";
//                $result[] = return_error_array($code_error, "", "( Nro de Orden " . $number . " Caidas: " . $sum_CAIDA . " ) " . $get_historic_data['RECUPERO'] . "/" . $get_temp_data['RECUPERO'] . "+" . $get_historic_data['INCOBRABLES_PERIODO'] . "/" . $get_temp_data['INCOBRABLES_PERIODO']);
//            }


            /* D.3 */
            $query_param = 'RECUPERO';
            $get_recuperos_tmp = $this->model_14->get_recuperos_tmp($filter, $query_param);
            foreach ($get_recuperos_tmp as $recuperos) {
                $caidas = $this->model_14->get_caida_tmp($filter);
                $return_calc = calc_anexo_14($caidas, $get_historic_data, $filter['warranty']);
                if ($return_calc) {
                    $code_error = "D.3";
                    $result[] = return_error_array($code_error, $filter['row'], "[" . $query_param . "] " . $return_calc);
                }
            }


            $query_param = 'INCOBRABLES_PERIODO';
            $get_recuperos_tmp = $this->model_14->get_recuperos_tmp($filter, $query_param);

            foreach ($get_recuperos_tmp as $recuperos) {
                $caidas = $this->model_14->get_caida_tmp($filter);
                $return_calc = calc_anexo_14($caidas, $get_historic_data, $filter['warranty']);
                if ($return_calc) {
                    $code_error = "E.3";
                    $result[] = return_error_array($code_error, $filter['row'], "[" . $query_param . "] " . $return_calc);
                }
            }


            /* Nro B.3
             * Detail:
             * Si se está informando un INCOBRABLE (Columna E del importador), debe validar que el número de garantía registre 
             * previamente en el sistema (o en el mismo archivo que se está importando) una caída. 
             */
            if ((int) $get_temp_data['INCOBRABLES_PERIODO'] > 0) {
                if ($sum_CAIDA == 0) {
                    $code_error = "B.3";
                    $result[] = return_error_array($code_error, $filter['row'], $get_temp_data['INCOBRABLES_PERIODO']);
                }
            }

            /* Nro B.4
             * Detail: 
             * Si se está informando un GASTOS POR GESTIÓN DE RECUPERO (Columna F), 
             * debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) una caída.
             */
            if ((int) $get_temp_data['GASTOS_EFECTUADOS_PERIODO'] > 0) {
                if ($sum_CAIDA == 0) {
                    $code_error = "B.4";
                    $result[] = return_error_array($code_error, $filter['row'], $get_temp_data['GASTOS_EFECTUADOS_PERIODO']);
                }
            }

            /* Nro B.5
             * Detail: 
             * Si se está informando un RECUPERO DE GASTOS POR GESTIÓN DE RECUPERO (Columna G), 
             * debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) un 
             * GASTO POR GESTIÓN DE RECUPERO. 
             */


            if ((int) $get_temp_data['RECUPERO_GASTOS_PERIODO'] != 0) {

                if ($sum_RECUPERO_GASTOS_PERIODO == 0) {
                    $code_error = "B.5";
                    $result[] = return_error_array($code_error, $filter['row'], $get_temp_data['GASTOS_EFECTUADOS_PERIODO']);
                }

                /* G.3 */

                if ($sum_GASTOS > $sum_GASTOS_EFECTUADOS_PERIODO) {
                    $code_error = "G.3";
                    $result[] = return_error_array($code_error, $filter['row'], "( Nro de Orden " . $filter['warranty'] . "(" . $filter['date'] . ") Gastos Efectuados: " . $sum_GASTOS_EFECTUADOS_PERIODO . " ) " . $get_historic_data['RECUPERO_GASTOS_PERIODO'] . "/" . $get_temp_data['RECUPERO_GASTOS_PERIODO'] . "+" . $get_historic_data['GASTOS_INCOBRABLES_PERIODO'] . "/" . $get_temp_data['GASTOS_INCOBRABLES_PERIODO']);
                }


                $query_param = 'RECUPERO_GASTOS_PERIODO';

                $get_gastos_tmp = $this->model_14->get_gastos_tmp($filter, $query_param);
                foreach ($get_gastos_tmp as $gastos) {
                    $return_calc = calc_anexo_14_gastos($gastos, $get_historic_data, $number);
                    if ($return_calc) {
                        $code_error = "G.3";
                        $result[] = return_error_array($code_error, $filter['row'], "[" . $query_param . "] " . $return_calc);
                    }
                }

                $query_param = 'GASTOS_INCOBRABLES_PERIODO';
                $get_gastos_tmp = $this->model_14->get_gastos_tmp($filter, $query_param);
                foreach ($get_gastos_tmp as $gastos) {
                    $return_calc = calc_anexo_14_gastos($gastos, $get_historic_data, $number);
                    if ($return_calc) {
                        $code_error = "G.3";
                        $result[] = return_error_array($code_error, $filter['row'], "[" . $query_param . "] " . $return_calc);
                    }
                }
            }

            /* Nro B.6
             * Detail: 
             * Si se está informando un INCOBRABLE DE GASTOS POR GESTIÓN DE RECUPERO (Columna G), 
             * debe validar que el número de garantía registre previamente en el sistema (o en el mismo archivo que se está importando) un 
             * GASTO POR GESTIÓN DE RECUPERO. 
             */
            if ((int) $get_temp_data['RECUPERO_GASTOS_PERIODO'] > 0) {
                if ($sum_RECUPERO_GASTOS_PERIODO == 0) {
                    $code_error = "B.6";
                    $result[] = return_error_array($code_error, $filter['row'], $get_temp_data['GASTOS_INCOBRABLES_PERIODO']);
                }
            }
        }

        /*
          debug($result);
          exit();
         */

        $this->data = $result;
    }

}