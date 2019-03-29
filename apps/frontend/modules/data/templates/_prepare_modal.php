<?php $ready = isset($ready) && $ready; ?>
<div class="modal hide" id="prepare-download" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php if ($ready): ?>
                <h5 class="modal-title">Export file generated. Download ready.</h5>
                <?php else: ?>
                <h5 class="modal-title">Processing file...</h5>
                <?php endif ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if (!$ready): ?>
                <a class="btn download-prepare" href="<?php echo url_for($prepare_route, array('id' => $id)) ?>"
                    data-submit='<?php echo json_encode($sf_data->getRaw('submit')) ?>'>Prepare</a>
                <?php endif ?>
            </div>
            <div class="modal-footer">
                <a class="btn btn-secondary" data-dismiss="modal">Close</a>
                <a <?php if (!$ready): ?>data-<?php endif ?>href="<?php echo url_for($prepare_route, array('id' => $id)) ?>?page=-1&amp;batch=<?php echo $batch ?>"
                    class="btn btn-primary download-ready" <?php if (!$ready): ?>disabled="disabled"
                    <?php endif ?>>Download</a>
            </div>
        </div>
    </div>
</div>