<?php

class Lib_124_data extends MX_Controller {
    /* VALIDADOR ANEXO 12 */

    public function __construct($parameter) {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('sgr/tools');
        $this->load->model('sgr/sgr_model');

        $model_anexo = "model_12";
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


        /**
         * BASIC VALIDATION
         * 
         * @param 
         * @type PHP
         * @name ...
         * @author Diego
         *
         * @example NRO_GARANTIA	FECHA_REAFIANZA	SALDO_VIGENTE	REAFIANZADO	RAZON_SOCIAL	CUIT
         * */
        for ($i = 0; $i <= count($parameterArr); $i++) {

            $param_col = (isset($parameterArr[$i]['col'])) ? $parameterArr[$i]['col'] : 0;

            /* NRO_GARANTIA
             * Nro A.1
             * Detail:
             * El Número de garantía debe estar informado en el sistema.
             */

            if ($param_col == 1) {
                $A_cell_value = "";
                $code_error = "A.1";



                $warranty_info = $this->$model_anexo->get_order_number_left($parameterArr[$i]['fieldValue']);
                //empty field Validation
                $return = check_empty($parameterArr[$i]['fieldValue']);
                if ($return) {
                    $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                    array_push($stack, $result);
                } else {
                    $A_cell_value = $parameterArr[$i]['fieldValue'];
                    foreach ($warranty_info as $info) {
                        $check_word = clean_spaces($info['5216'][0]);
                        $amount = $info['5218'];
                    }

                    if (!$warranty_info) {
                        $result = return_error_array($code_error, $parameterArr[$i]['row'], "Tipo no permitido" . $check_word);
                        array_push($stack, $result);
                    }

//                        $allow_words = array("GFMFO", "GC1", "GC2", "GT");
//                        $return = check_word($check_word, $allow_words);
//                        if ($return) {
//                            $result = return_error_array($code_error, $parameterArr[$i]['row'], "Tipo no permitido" . $check_word);
//                            array_push($stack, $result);
//                        }
                }
            }

            /* FECHA_REAFIANZA
             * Nro B.1
             * Detail:
             * Campo con formato de fecha. Numérico de cinco dígitos sin decimales.
             * Nro B.2
             * Detail:
             * La fecha debe ser igual o posterior a la fecha de entrada en vigencia de la garantía que está registrada en el sistema.
             * Nro B.3
             * Detail:
             * La fecha debe estar comprendida dentro del período informado.
             */

            if ($param_col == 2) {
                $code_error = "B.1";
                //empty field Validation
                $return = check_empty($parameterArr[$i]['fieldValue']);
                if ($return) {
                    $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                    array_push($stack, $result);
                }
                //Check Date Validation
                if (isset($parameterArr[$i]['fieldValue'])) {
                    $return = check_date_format($parameterArr[$i]['fieldValue']);
                    if ($return) {
                        $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                        array_push($stack, $result);
                    }


                    $B_cell_date_format = strftime("%Y-%m-%d", mktime(0, 0, 0, 1, -1 + $parameterArr[$i]['fieldValue'], 1900));

                    foreach ($warranty_info as $nro_orden) {
                        $datetime1 = new DateTime($nro_orden['5215']);
                        $datetime2 = new DateTime($B_cell_date_format);
                        $interval = $datetime1->diff($datetime2);
                        $result_dates = (int) $interval->format('%R%a');

                        if ($result_dates < 1) {
                            $code_error = "B.2";
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }
                    }



                    $code_error = "B.3";
                    /* PERIOD */
                    $return = check_period($parameterArr[$i]['fieldValue'], $this->session->userdata['period']);
                    if ($return) {
                        $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                        array_push($stack, $result);
                    }
                }
            }

            /* SALDO_VIGENTE
             * Nro C.1
             * Detail:
             * Formato de número. Acepta hasta dos decimales.
             */
            if ($param_col == 3) {
                //empty field Validation
                $code_error = "C.1";

                $return = check_empty($parameterArr[$i]['fieldValue']);
                if ($return) {
                    $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                    array_push($stack, $result);
                } else {
                    $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                    if ($return) {
                        $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                        array_push($stack, $result);
                    }
                }
            }

            /* REAFIANZADO
             * Nro D.1
             * Detail:
             */
            if ($param_col == 4) {
                //empty field Validation
                $code_error = "D.1";



                $return = check_empty($parameterArr[$i]['fieldValue']);
                if ($return) {
                    $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                    array_push($stack, $result);
                } else {


                    $code_error = "D.1";
                    if ($parameterArr[$i]['fieldValue'] != "") {
                        $return = check_decimal($parameterArr[$i]['fieldValue'], 2, true);
                        if ($return) {
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        } else {
                            /* Formato de número. Acepta hasta dos decimales.  Debe ser mayor a cero. */

                            $float_var = ((float) $parameterArr[$i]['fieldValue']) * 100;

                            $result = check_is_numeric_range($float_var, 0, 100);
                            if (!$result) {
                                $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                                array_push($stack, $result);
                            }
                        }
                    }
                }
            }


            /* RAZON_SOCIAL
             * Nro E.1
             * Detail:
             * En caso de que el CUIT informado en la Columna E ya está registrado en la Base de Datos del Sistema, este tomará en cuenta el nombre allí registrado. En caso contrario, se mantendrá provisoriamente el nombre informado por la SGR.
             */
            if ($param_col == 5) {
                //empty field Validation
                $code_error = "E.1";

                $return = check_empty($parameterArr[$i]['fieldValue']);
                if ($return) {


                    $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                    array_push($stack, $result);
                }
            }

            /* CUIT
             * Nro F.1
             * Detail:
             * Debe tener 11 caracteres sin guiones. Debe validar que cumpla con el “ALGORITMO VERIFICADOR”.
             */
            if ($param_col == 6) {
                //empty field Validation
                $code_error = "F.1";

                $return = check_empty($parameterArr[$i]['fieldValue']);
                if ($return) {


                    $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                    array_push($stack, $result);
                }

                //cuit checker
                if (isset($parameterArr[$i]['fieldValue'])) {
                    $return = cuit_checker($parameterArr[$i]['fieldValue']);
                    if (!$return) {


                        $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                        array_push($stack, $result);
                    }
                }
            }
        } // END FOR LOOP->


        $this->data = $stack;
    }

}