<?php

class Lib_123_data extends MX_Controller {
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
        $cell_values = array();


        for ($i = 1; $i <= $parameterArr[0]['count']; $i++) {

            /**
             * BASIC VALIDATION
             * 
             * @param 
             * @type PHP
             * @name ...
             * @author Diego
             *
             * @example NRO_ORDEN	DIA1	DIA2	DIA3	DIA4	DIA5	DIA6	DIA7	DIA8	DIA9	DIA10	DIA11	DIA12	DIA13	DIA14	DIA15	DIA16	DIA17	DIA18	DIA19	DIA20	DIA21	DIA22	DIA23	DIA24	DIA25	DIA26	DIA27	DIA28	DIA29	DIA30	DIA31
             * */
            for ($i = 0; $i <= count($parameterArr); $i++) {

                /* NRO_ORDEN
                 * Nro A.1
                 * Detail:
                 * El Número de garantía debe estar informado en el sistema y corresponder con alguno de los siguientes tipos de garantías:
                  GFMFO
                  GC1
                  GC2
                  GT
                 */
                //debug($parameterArr);

                if ($parameterArr[$i]['col'] == 1) {

                    $A_cell_value = "";
                    $code_error = "A.1";
                    //empty field Validation
                    $return = check_empty($parameterArr[$i]['fieldValue']);
                    if ($return) {
                        $result = return_error_array($code_error, $parameterArr[$i]['row'], "empty");
                        array_push($stack, $result);
                    } else {
                        $A_cell_value = $parameterArr[$i]['fieldValue'];
                    }
                }
                
                
                
                
                /* DIA
                 * Nro B.1
                 * Detail:
                 * Los montos informados deben ser menores o iguales al Monto de Garantía Otorgado registrado en el Sistema. 
                 * Nro B.2
                 * Detail:
                 * Si algún día el saldo estuvo en Cero, deben informar “0”. Ningún campo puede estar vacío. 
                 */
                if ($parameterArr[$i]['col'] != 1) {

                    $value = $parameterArr[$i]['fieldValue'];
                    $key = $parameterArr[$i]['col'];
                    
                    if ($value == "") {
                        $code_error = "B.2";
                        $result = return_error_array($code_error, $row, "El Día " . $key . " No puede estar vacio");
                        array_push($stack, $result);
                    } else {


                        $warranty_info = $this->$model_anexo->get_order_number_left($A_cell_value);
                        
                        foreach ($warranty_info as $info) {
                            
                            $check_word = $info['5216'][0];
                            $amount = $info['5218'];
                            $origin = $info['5215'];
                            $currency = $info['5219'][0];
                        }



                        $allow_words = array("GFMFO", "GC1", "GC2", "GT");
                        $return = check_word($check_word, $allow_words);
                        if ($return) {
                            $code_error = "A.1";
                            $result = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                            array_push($stack, $result);
                        }


                        if ($currency == 2) {
                            /* DOLLAR */
                            $dollar_quotation_origin = $this->sgr_model->get_dollar_quotation(translate_date_xls($origin));
                            $dollar_quotation_period = $this->sgr_model->get_dollar_quotation_period();
                            $new_dollar_value = ($amount / $dollar_quotation_origin) * $dollar_quotation_period;

                            // var_dump($new_dollar_value .">". $amount, $value);

                            if ($new_dollar_value < $value) {
                                $code_error = "B.1.B";
                                $result = return_error_array($code_error, $parameterArr[$i]['row'], 'El Día ' . $key . ' $' .$value.' Monto disponible para el Nro. Orden  = '. $A_cell_value.'  (' . $amount .'/'. $dollar_quotation_origin .'*'. $dollar_quotation_period . ' = '.$new_dollar_value.' )');
                                array_push($stack, $result);
                            }
                        } else {
                            
                            if ($value > $amount) {
                                $code_error = "B.1.A";
                                $result = return_error_array($code_error, $row, "El Día " . $key . " (" . $value . ")");
                                array_push($stack, $result);
                            }
                        }

                        $return = check_decimal($value, 2, true);
                        if ($return) {
                            $code_error = "B.3";
                            $result = return_error_array($code_error, $row, "El Día" . $key . " (" . $value . ")");
                            array_push($stack, $result);
                        }
                    }
                }               
            } // END FOR LOOP->
        }
        
        $this->data = $stack;
    }

}
