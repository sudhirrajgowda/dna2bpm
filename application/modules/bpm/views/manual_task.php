<!-- Modal -->
<div id="myModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{task_name}</h3>
            </div>
            <div class="modal-body">
                <p>{task_documentation}</p>
                {DataObject_Input}
                <div class="file-input" resourceId="{resourceId}">
                    <h4>
                        <i class='icon icon-arrow-right'></i> 
                        <i class='icon icon-files'></i> 
                        {properties name}
                    </h4>
                    <p>{properties documentation}</p>
                    {ui}
                </div>
                {/DataObject_Input}
            </div>
            <div class="modal-footer">
                <button id="closeTask" class="btn pull-left btn-danger" data-dismiss="modal" aria-hidden="true">
                    <i class="icon-play icon-chevron-left icon-white"></i>
                    {lang closeTask}
                </button>
                <button id="finishTask" class="btn btn-success">
                    <i class="icon-play icon-white"></i>
                    {lang finishTask}
                </button>
            </div>
        </div>
    </div>
</div>