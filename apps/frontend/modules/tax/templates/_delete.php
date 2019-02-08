<?php use_helper('I18N') ?>
<div class="modal hide hidden_remove" id="tax_country_delete_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alert</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure to <strong>delete</strong> the tax country "<b><?php echo format_country($country) ?></b>"?</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('tax_delete', array('id' => $id)) ?>">Yes</a>
                <a class="btn btn-primary" data-dismiss="modal">No</a>
            </div>
        </div>
    </div>
</div>