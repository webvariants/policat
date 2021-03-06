<div class="modal hide hidden_remove" id="meta_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fogotten_modal_label">Alert</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure to <strong>irrevocably delete</strong> the meta field "<b><?php echo $name ?></b>"?</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('target_meta_delete', array('id' => $id)) ?>">Yes. Delete meta field</a>
                <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>