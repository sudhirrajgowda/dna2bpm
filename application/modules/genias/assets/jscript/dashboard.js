/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$( document ).ready(function() {





//$( ".datepicker" ).datepicker({dateFormat: "dd-mm-yy"});

 $('#dp3').datepicker();


$('#form_goals a').click(function(){
    var data=$('#form_goals').serializeArray();

    $.post(globals.module_url+'add_goal',{'data':data},function(resp){
      location.reload();

    });
});

$(".detalle").click(function(){
   $(this).next('.observaciones').slideToggle();
});

// localStorage guardo datos usuario
var userdata={idu:1,genia:1};
localStorage['userdata']="";

// Desabilito los anchos que etan desabled 
$('a[disabled]').one('click', function(e){
e.preventDefault();
});

});

