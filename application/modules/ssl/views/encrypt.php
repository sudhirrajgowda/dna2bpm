<div class="box-body" style="">
    <form id="form_encrypt"> 
       <!-- Description -->
        <div class="form-group">
            <label>Fingerprint</label>
            <input class="form-control" name="fingerprint"  placeholder="Fingerprint">
        </div>
                     

        <!-- Mensaje -->
        <div class="form-group">
            <label>Plain text</label>
            <textarea class="form-control" name="plain_text" rows="6" placeholder="PLain text"></textarea>
        </div>
        
         <!-- area for msgs -->                       
        <div class="form-group">
            <label>Encrypted text (base64 encoded)</label>
            <textarea class="form-control" name="encrypted_text" rows="6" placeholder=""></textarea>
        </div>
        
        <!-- Send -->                    
        <div class="input-group-btn">
            <button  type="submit" class="btn btn-default btn-flat btn-block"><i class="fa fa-floppy-o"></i> Encrypt!</button>
        </div>                        
    </form>   
</div>