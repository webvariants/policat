<div class="modal hide hidden_remove" id="campaign_hard_delete_modal"  tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alert</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Do you really want to permanently delete the campaign "<b><?php echo $name ?></b>" including all actions and data therein? This cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>' href="<?php echo url_for('campaign_hard_delete', array('id' => $id)) ?>">Yes</a>
                <a class="btn btn-primary" data-dismiss="modal">No</a>
            </div>
        </div>
    </div>
</div>