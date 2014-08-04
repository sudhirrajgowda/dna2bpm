<!-- INBOX WIDGET -->
       {if {reply}}
         <input type="hidden" name="reply" value="1" />
         <input type="hidden" name="reply_name" value="{reply_name}" />
         <input type="hidden" name="reply_title" value="{reply_title}" />
         <input type="hidden" name="reply_body" value="{reply_body}" />
         <input type="hidden" name="reply_idu" value="{reply_idu}" />
          <input type="hidden" name="reply_date" value="{reply_date}" />
        {/if}


<form class="form-horizontal" id="new_msg">
<!--  To -->
      
  <div class="form-group">
    <label class="col-sm-2 control-label">To:</label>
    <div class="col-sm-10">
		    <input type="hidden" name="to" class="select2 form-control"   multiple="multiple" />
  </div>
  </div>
<!--  Title -->
  <div class="form-group">
    <label class="col-sm-2 control-label">Subject:</label>
    <div class="col-sm-10">
    <input type="text" name="subject" class="form-control"  placeholder="Subject">
     </div>
  </div>
<!--  MSG -->
  <div class="form-group">
    <label class="col-sm-2 control-label">Body:</label>
    <div class="col-sm-10">
     <textarea rows="5" name="body" placeholder="Body" class="form-control"></textarea>
     </div>
  </div>
 <!--  SEND -->
  <div class="form-group">
    <label class="col-sm-10 control-label"></label>
    <div class="col-sm-2">
      <button type="submit" class="btn btn-primary ">Send</button>
     </div>
  </div> 

  

  
</form>

<script>
 $(document).ready(function(){

	  // ===== AJAX SELECT BOX
	  //$('.select2').select2();
	  $('.select2').select2({
	        placeholder: "To..",
 	        dataType: 'json',
	       	multiple:true,
	        ajax: {
	        type:"POST",     
	        url:	globals.base_url+"inbox/inbox/get_users",
	        data: function (term) {
	            return {
	                term: term          
	             };
	        },
	        results: function (result) {
		        console.log(result);
	            return result;
	        }
	        }

	    });

	  $(document).on('submit','#new_msg',function(e){
		e.preventDefault();
		var data=$(this).serializeArray();
		$.post(globals.base_url+"inbox/inbox/send",{data:data},function(resp){
	        $('#myModal').find('.modal-body').html('Message sent!');
		});
		
	  });

		
 });

</script>
