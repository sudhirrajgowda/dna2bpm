/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. --- ----
 */

jQuery(document).ready(function($) {

/*ANEXOS*/
$('#anexo').click(function() {
   var loc = $('#select_anexos option:selected').val();
    window.open(loc, '_self');
    return false;
});

/*ANEXOS FRE*/
$('#anexo_fre').click(function() {
   var loc = $('#select_anexos_fre option:selected').val();
    window.open(loc, '_self');
    return false;
});

/*REPORTE*/
$('#report').click(function() {
   var loc = $('#select_reports option:selected').val();
    window.open(loc, '_blank');
    return false;
});



$("#consult").validate({
  rules: {
   // simple rule, converted to {required:true}
    cuit: "required"    
  },
  
    submitHandler: function(form) {
        var data=$(form).serializeArray();       
        

        /*HIDE*/
        $('#msg_error').hide();
        $('#A').hide();
        $('#B').hide();
        $('#loading').show();
        var cuit = null;

        var url=base_url+"sgr/consultas/process/";        
        var url_print=base_url+"sgr/consultas/certificado/";    
        var url_detail=base_url+"sgr/consultas/source/";   
        $.ajax({
          type: "POST",
          url: url,
          data: data,
          success: function(resp) {                                       
              $('.cuit_all').hide();
              var cuit = null;
              cuit = $("#cuit").val();                            
              $('#loading').hide();      
              switch(resp.tipo_socio)
              {
                
                case 'A':                      
                      $("#a_cuit").html(resp.cuit);
                      $("#a_rs").html(resp.rs);
                      $('#A').show(); 
                      $("#a_vinculado").html(resp.vinculados);                         
                break;

                case 'B':
                    $("#b_cuit").html(resp.cuit);
                    $("#b_rs").html(resp.rs);
                      $('#B').show();  
                      $("#b_vinculado").html(resp.vinculados);  
                break;

                default:
                    $("#e_cuit").html($('#cuit').val());
                    $('#msg_error').show();
                break;
              }
              
          },
          dataType: 'json'
        });
    }
});




   

  
    

//==  ready
});