<div class = "row" >
    
<!-- Tramites -->

<div class = "col-md-3"  >
    <div class='row center-block dashboard_shortcut' data-id='tramites' style=''>
       <div class='title'><i class="fa fa-paperclip fa-2x" aria-hidden="true"></i><span>Trámites</span></div>

    </div>
    <div class='tramites_shortcut_extra' style='display:none;position:relative;margin-bottom:10px;top:-10px;padding:8px' >
        {tramites_extra}

      </div> 
</div>

<!-- Mis tramites -->

<div class = "col-md-3 load_tiles_after" data-id='mis_tramites' href='{base_url}/bpm/bpmui/widget_cases/1/5/fondo_semilla2016' >
    <div class='row center-block dashboard_shortcut'>
       <div class='title'><i class="fa fa-paperclip fa-2x" aria-hidden="true"></i><span>Mis Trámites</span></div>
       <div class="label label-{mistramites_count_label_class} pull-right">{mistramites_count_qtty}</div>
    </div>
</div>


<!-- Tareas -->

<div class = "col-md-3 load_tiles_after" data-id='mis_tareas' href='{base_url}/bpm/bpmui/widget_2do/1/5/fondo_semilla2016'>
    <div class='row center-block dashboard_shortcut'>
       <div class='title'><i class="fa fa-list-alt fa-2x" aria-hidden="true"></i><span>Mis Tareas</span></div>
       <div class="label label-{tareas_count_label_class} pull-right">{tareas_count_qtty}</div>
    </div>
</div>



<!-- Notificaciones -->

<div class = "col-md-3 " data-id='inbox' >
    <div class='row center-block dashboard_shortcut'>
       <div class='title'><i class="fa fa-envelope fa-2x" aria-hidden="true"></i><span>Notificaciones</span></div>
       <div class="label label-{inbox_count_label_class} pull-right">{inbox_count_qtty}</div>
    </div>
</div>



</div>