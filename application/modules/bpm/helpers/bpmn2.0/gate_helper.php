<?php

function run_ParallelGateway($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;

    $has_finished = true;
//---get inbound shapes
    $resourceId = $shape->resourceId;
    $inbound = $CI->bpm->get_inbound_shapes($resourceId, $wf);
//---only mark finished if all inbound finished
    foreach ($inbound as $inshape) {
        $token = $CI->bpm->get_token($wf->idwf, $wf->case, $inshape->resourceId);
        if ($token['status'] != 'finished') {
            $has_finished = false;
        }
    }
    if ($has_finished) {
        $CI->bpm->movenext($shape, $wf);
    } else {
        $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting');
    }
}

function run_Exclusive_Databased_Gateway($shape, $wf, $CI) {

    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    $debug = false;
    $iduser = (int) $CI->idu;
    $user = $CI->user->get_user($iduser);
    $user_groups = (array) $user->group;
    //----ASSIGN to USER / GROUP
//    $CI->bpm->assign($shape, $wf);
//    //----Get token data
//    $token = $CI->bpm->get_token($wf->idwf, $wf->case, $shape->resourceId);
    $shape_data = array();
////---assign gate to current user
//    //$shape_data['assign'][] = (int) $CI->session->userdata('iduser');
//    ////////////////////////////////////////////////////////////////////////////
/////////////////////////EVAL EXECUTION POLICY////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
////--by default user is not allowed to execute this task
////--except assign or group says otherwise
//    $is_allowed = false;
//    if ($debug)
//        echo "Eval is_allowed<br/>";
////---check if the user is assigned to the task
//    if (isset($token['assign'])) {
//        if (in_array($iduser, $token['assign'])) {
//            $is_allowed = true;
//            if ($debug)
//                echo "is_allowed=true user is in token assign<br/>";
//        }
//    }
//
//
////---check if user belong to the group the task is assigned to
////---but only if the task havent been assigned to an specific user
//    if (isset($token['idgroup']) and !isset($token['assign'])) {
//        foreach ($user_groups as $thisgroup) {
//            if (in_array((int) $thisgroup, $token['idgroup'])) {
//                $is_allowed = true;
//                if ($debug)
//                    echo "is_allowed=true user is in token group<br/>";
//            }
//        }
//    }
//
//
//    if (!$is_allowed) {
//        if ($debug)
//            echo "is_allowed=false<br/>";
//        return false;
//    }
////////////////////////////////////////////////////////////////////////////
    extract((array) $CI->data);
    if ($debug)
        var_dump('DATA', $CI->data);
//$cond=eval('return '.$shape->properties->gates_assignments.';');
//var_dump($shape->properties->gates_assignments,$cond);
//echo '<hr>';
//--get outgoing rules
    $result = array();
    $i = 0; ///----count ammount of true cases
//var_dump($shape);
    $assignment = $shape->properties->gates_assignments;
//---if has assignment then evaluate
    if ($assignment) {
        foreach ($shape->outgoing as $key => $out) {
            $shape_out = $CI->bpm->get_shape($out->resourceId, $wf);
            if ($shape_out->stencil->id == 'SequenceFlow') {
                $op = '==';
                $cond = $shape_out->properties->conditionexpression;
                $op_map = array(
                    '>=',
                    '>',
                    '<=',
                    '<',
                    '<>',
                    '!='
                );
//----parse $op
                foreach ($op_map as $operation) {
                    if (strstr($cond, $operation)) {
                        $cond = str_replace($operation, '', $cond);
                        $op = $operation;
                        break;
                    }
                }

                if ($cond == '')
                    $cond = 'false';
//$streval = "return (" . $assignment . ")==('" . (string) $shape_out->properties->conditionexpression . "');";
//---replace lang true/false
                $true = strtolower($CI->lang->line('true'));
                $false = strtolower($CI->lang->line('false'));
                $cond = (strtolower($cond) == $true) ? 'true' : $cond;
                $cond = (strtolower($cond) == $false) ? 'false' : $cond;

                $streval = "return (" . $assignment . ")$op(" . (string) $cond . ");";
//--- post process eval
                if (strstr($cond, ',')) {
                    $streval = "return (in_array(" . $assignment . ",explode(',','$cond')));";
                }
                if ($debug)
                    var_dump('$streval', $streval);
                $result[$shape_out->resourceId]['streval'] = $streval;
                $result[$shape_out->resourceId]['shape'] = $shape_out;

                try {
                    $result[$shape_out->resourceId]['eval'] = eval($streval);
                } catch (Exception $e) {
                    echo 'error in eval: ', $e->getMessage(), "\n";
                }
                if ($result[$shape_out->resourceId]['eval'])
                    $i++;
                if ($debug) {
                    var_dump($assignment, eval("return($assignment);"), $result[$shape_out->resourceId]['eval']);
                    echo '<hr>';
                }
            }
        }

        if ($i == 0) {//---none of the above has match, so try to find default
            foreach ($shape->outgoing as $key => $out) {
                $shape_out = $CI->bpm->get_shape($out->resourceId, $wf);
                if ($shape_out->properties->conditiontype == 'Default') {
                    $result[$shape_out->resourceId]['streval'] = 'Default';
                    $result[$shape_out->resourceId]['eval'] = true;
                    $i++;
                }
            }
        }
//exit;        
//---process all Sequences and only activate one
        if ($i == 1) {
//----mark shape as finished
            $CI->bpm->movenext($shape, $wf, $result, false); //----don't process outgoing flows
            foreach ($result as $resourceId => $thisresult) {
                $shape_out = $thisresult['shape'];
                $thisresult['shape'] = '';
//---add result to data
                if ($thisresult['eval']) {
                    $shape_out = $CI->bpm->get_shape($resourceId, $wf);
                    $CI->bpm->movenext($shape_out, $wf, $shape_data);
                } else {
//do nothing
// $CI->bpm->set_token($wf->idwf, $wf->case, $shape_out->resourceId, $shape_out->stencil->id, 'stoped', $shape_data);
                }
            }
        } else {

            show_error("The are more than one valid option,  or none for " . $shape->properties->name . ':' . $shape->properties->gates_assignments);
        }
    } else {
//Check if has to be procesed manually
        $do_manual = false;
        foreach ($shape->outgoing as $key => $out) {
            $shape_out = $CI->bpm->get_shape($out->resourceId, $wf);
//var_dump($shape_out);
            if (isset($shape_out->properties->conditionexpression)) {
                if (trim($shape_out->properties->conditionexpression) <> '')
                    $do_manual = true;
            }
        }
        if ($do_manual) {
//---assign proper user or group like in Task
            $CI->bpm->assign($shape, $wf);
            $shape_data['tasktype'] = 'User'; //--this is for Tasks panel
            $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'user', $shape_data);
//$CI->manual_gate('model', $wf->idwf, $wf->case, $shape->resourceId);
        } else {
//---- is an xor Join then first to come then out!
            $CI->bpm->movenext($shape, $wf);
        }
    }
}

function run_InclusiveGateway($shape, $wf, $CI) {
//---same as Exclusive but more than one outgoing can be true
//---Incoming flow must be synchronized like in paralell.

    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
//$debug = true;
    $shape_data = array();
//---assign gate to current user
    $shape_data['assign'][] = (int) $CI->session->userdata('iduser');
    extract((array) $CI->data);
    if ($debug)
        var_dump('DATA', $CI->data);
//$cond=eval('return '.$shape->properties->gates_assignments.';');
//var_dump($shape->properties->gates_assignments,$cond);
//echo '<hr>';
//--get outgoing rules
    $result = array();
    $i = 0; ///----count ammount of true cases
//var_dump($shape);
    $assignment = $shape->properties->gate_assignments;
//---if has assignment then evaluate
    if ($assignment) {
        foreach ($shape->outgoing as $key => $out) {

            $shape_out = $CI->bpm->get_shape($out->resourceId, $wf);
//var_dump($shape_out);
            $streval = "return (" . $assignment . ")==('" . (string) $shape_out->properties->conditionexpression . "');";
            if ($debug)
                var_dump($streval);
            $result[$shape_out->resourceId]['streval'] = $streval;
            $result[$shape_out->resourceId]['eval'] = eval($streval);
            $result[$shape_out->resourceId]['shape'] = $shape_out;

            if ($result[$shape_out->resourceId]['eval'])
                $i++;
            if ($debug) {
                var_dump($assignment, $result[$shape_out->resourceId]['eval']);
                echo '<hr>';
            }
        }
//---process all Sequences and only activate one
        if ($i >= 1) {
//----mark shape as finished
            $CI->bpm->movenext($shape, $wf);
//-->movenext on all 'true' evals
            foreach ($result as $thisresult) {
                $shape_out = $thisresult['shape'];
                $thisresult['shape'] = '';
//---add result to data
                $shape_data+=$thisresult;
                if ($thisresult['eval']) {
                    $CI->bpm->movenext($shape_out, $wf, $shape_data);
                } else {
                    $CI->bpm->set_token($wf->idwf, $wf->case, $shape_out->resourceId, $shape_out->stencil->id, 'stoped', $shape_data);
                }
            }
        } else {

            show_error("The has to be at least one valid option " . $shape->properties->name . ':' . $shape->properties->gates_assignments);
        }
    } else {
//Check if has to be procesed manually
        $do_manual = false;
        foreach ($shape->outgoing as $key => $out) {
            $shape_out = $CI->bpm->get_shape($out->resourceId, $wf);
//var_dump($shape_out);
            if (isset($shape_out->properties->conditionexpression)) {
                if (trim($shape_out->properties->conditionexpression) <> '')
                    $do_manual = true;
            }
        }
        if ($do_manual) {
            $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'user', $shape_data);
//$CI->manual_gate('model', $wf->idwf, $wf->case, $shape->resourceId);
        } else {
//---- SYNCHRONIZE INCOMING FLOWS
            $has_finished = true;
//---get inbound shapes
            $resourceId = $shape->resourceId;
            $inbound = $CI->bpm->get_inbound_shapes($resourceId, $wf);
//---only mark finished if all inbound finished
            foreach ($inbound as $inshape) {
                $token = $CI->bpm->get_token($wf->idwf, $wf->case, $inshape->resourceId);
                if ($token['status'] != 'finished') {
                    $has_finished = false;
                }
            }
            if ($has_finished) {
                $CI->bpm->movenext($shape, $wf);
            } else {
                $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'waiting');
            }
        }
    }
    
}

function run_EventbasedGateway($shape, $wf, $CI) {
    $debug = (isset($CI->debug[__FUNCTION__])) ? $CI->debug[__FUNCTION__] : false;
    // $debug=true;
    /**
     * @todo: don't process if no new events
     */
    $has_finished = true;
    $flows=$CI->bpm->get_outgoing_shapes($shape, $wf);
    $cancel_events=false;
    foreach ($flows as $flow) {
            $trigger_resourceId=$flow->outgoing->{0}->resourceId;
            $token_trigger=$CI->bpm->get_token($wf->idwf, $wf->case, $trigger_resourceId);
            // var_dump2($token_trigger);
            //---if not exists the initialize tokens for engine
            if(!$token_trigger){
                $shape_trigger=$CI->bpm->get_shape($trigger_resourceId, $wf);
                //$CI->bpm->movenext($shape_trigger,$wf);
                $token_trigger=$CI->bpm->token_checkin(array(), $wf, $shape_trigger);
                $token_trigger['status']='pending';
                $CI->bpm->save_token($token_trigger);
            }
            
            $trigger_status=$token_trigger['status'];
            //---Add shapes to be canceled
            if($trigger_status=='finished'){
                
                //---set flag for process cancelations
                $cancel_events=true;
                //---set finished to flow preceeding
                $CI->bpm->set_token($wf->idwf, $wf->case, $flow->resourceId, $flow->stencil->id, 'finished');
            } else {
                $triggers[]=array(
                'flow'=>$flow,
                'shape'=>$CI->bpm->get_shape($trigger_resourceId, $wf));
            }
            
        }
    //---process cancelations
    if($cancel_events){
        if($debug) echo "process Cancelations<br/>";
        $data = array('canceledBy' => $shape->resourceId, 'canceledName' => $shape->properties->name);
        foreach($triggers as $arr){
            $flow=$arr['flow'];
            $shape_cancel=$arr['shape'];
            $CI->bpm->set_token($wf->idwf, $wf->case, $shape_cancel->resourceId, $shape_cancel->stencil->id, 'canceled', $data);
        }
        //---now set $shape as finished
        $CI->bpm->set_token($wf->idwf, $wf->case, $shape->resourceId, $shape->stencil->id, 'finished');
    }
}