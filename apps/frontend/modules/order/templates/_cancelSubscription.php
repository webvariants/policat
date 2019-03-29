<?php use_helper('Number') ?>
<div class="modal hide hidden_remove" id="cancel_subscription_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Campaign: <?php echo $campaign->getName() ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <p>Do you really want to cancel your subscription? Your actions will be ended when your package expires
                    or your credits are exhausted.</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger ajax_link post"
                    data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token)) ?>'
                    href="<?php echo url_for('order_cancel_subscription', array('id' => $id)) ?>">Proceed and cancel
                    subscription</a>
                <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>