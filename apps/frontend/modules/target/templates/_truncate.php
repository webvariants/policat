<div class="modal hide hidden_remove" id="contact_truncate_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alert</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure to <strong>irrevocably delete</strong> all contacts of the target list
                    "<b><?php echo $name ?></b>"?</p>
                <p>Note: in case this target list is used by a pledge action, deleting the contacts will also delete all
                    pledges received!</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger ajax_link post"
                    data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>'
                    href="<?php echo url_for('target_truncate', array('id' => $id)) ?>">Yes. Delete all contact
                    records</a>
                <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>