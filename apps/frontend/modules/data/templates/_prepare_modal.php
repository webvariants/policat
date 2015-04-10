<div class="modal hide" id="prepare-download">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>Processing file...</h3>
    </div>
    <div class="modal-body">
        <a class="btn download-prepare" href="<?php echo url_for($prepare_route, array('id' => $id)) ?>" data-submit='<?php echo json_encode($sf_data->getRaw('submit')) ?>'>Prepare</a>
        
    </div>
    <div class="modal-footer">
        <a class="btn" data-dismiss="modal">Close</a>
        <a data-href="<?php echo url_for($prepare_route, array('id' => $id)) ?>?page=-1&amp;batch=<?php echo $batch ?>" class="btn btn-primary download-ready" disabled="disabled">Download</a>
    </div>
</div>