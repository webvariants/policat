<?php $ready = isset($ready) && $ready; ?>
<div class="modal hide" id="prepare-download">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <?php if ($ready): ?>
        <h3>Export file generated. Download ready.</h3>
        <?php else: ?>
        <h3>Processing file...</h3>
        <?php endif ?>
    </div>
    <div class="modal-body">
        <?php if (!$ready): ?>
        <a class="btn download-prepare" href="<?php echo url_for($prepare_route, array('id' => $id)) ?>" data-submit='<?php echo json_encode($sf_data->getRaw('submit')) ?>'>Prepare</a>
        <?php endif ?>
    </div>
    <div class="modal-footer">
        <a class="btn btn-secondary" data-dismiss="modal">Close</a>
        <a <?php if (!$ready): ?>data-<?php endif ?>href="<?php echo url_for($prepare_route, array('id' => $id)) ?>?page=-1&amp;batch=<?php echo $batch ?>" class="btn btn-primary download-ready" <?php if (!$ready): ?>disabled="disabled"<?php endif ?>>Download</a>
    </div>
</div>