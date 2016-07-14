<?php

class Lib_16_data extends MX_Controller {
    /* VALIDADOR ANEXO 16 */

    public function __construct($parameter) {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('sgr/tools');
        $this->load->model('sgr/sgr_model');

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



        /**
         * BASIC VALIDATION
         * 
         * @param 
         * @type PHP
         * @name ...
         * @author Diego             
         * @example        
         * SALDO_PROMEDIO_GARANTIAS_VIGENTES     
          SALDO_PROMEDIO_PONDERADO_GARANTIAS_VIGENTES_80_HASTA_FEB_2010
          SALDO_PROMEDIO_PONDERADO_GARANTIAS_VIGENTES_120_HASTA_FEB_2010
          SALDO_PROMEDIO_PONDERADO_GARANTIAS_VIGENTES_80_DESDE_FEB_2010
          SALDO_PROMEDIO_PONDERADO_GARANTIAS_VIGENTES_120_DESDE_FEB_2010
          SALDO_PROMEDIO_PONDERADO_GARANTIAS_VIGENTES_80_DESDE_ENE_2011
          SALDO_PROMEDIO_PONDERADO_GARANTIAS_VIGENTES_120_DESDE_ENE_2011
          SALDO_PROMEDIO_FDR_TOTAL_COMPUTABLE
          SALDO_PROMEDIO_FDR_CONTINGENTE
         * */
        for ($i = 0; $i <= count($parameterArr); $i++) {

            $param_col = (isset($parameterArr[$i]['col'])) ? $parameterArr[$i]['col'] : 0;

            /* DESCRIPCION
             * Nro BJ.1
             * Detail:
             * Debe contener formato numérico sin decimales.
             */



            $range = range(1, 9);
            if (in_array($param_col, $range)) {               
              

                $code_error = "A.1";
                if (empty($parameterArr[$i]['fieldValue']) && $parameterArr[$i]['fieldValue'] != '0') {
                    $result[] = return_error_array($code_error, $param_col . " " . $parameterArr[$i]['row'], "empty");
                } else {
                    $return = (string) check_is_numeric_no_decimal($parameterArr[$i]['fieldValue']);
                    if ($return == null) {
                        $result[] = return_error_array($code_error, $parameterArr[$i]['row'], $parameterArr[$i]['fieldValue']);
                    }
                }
            }
        } 
        // END FOR LOOP->
        /*var_dump($result);
        exit();*/
        $this->data = $result;
    }

}