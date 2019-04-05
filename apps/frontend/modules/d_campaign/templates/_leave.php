<div class="modal hide hidden_remove" id="campaign_leave_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave camapign "<b><?php echo $name ?></b>"</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Attention: If you leave now, you will lose access to any action within this campaign!</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger ajax_link post"
                    data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>'
                    href="<?php echo url_for('campaign_leave', array('id' => $id)) ?>">Leave</a>
                <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>