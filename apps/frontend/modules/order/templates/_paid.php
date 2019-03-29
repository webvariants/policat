<?php use_helper('Number') ?>
<div class="modal hide hidden_remove" id="order_paid_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order <?php echo $id ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure to approve payment of
                    <strong><?php echo format_currency($price, StoreTable::value(StoreTable::BILLING_CURRENCY)) ?></strong>
                    for order <b><?php echo $id ?></b>?</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger ajax_link post"
                    data-submit='<?php echo json_encode(array('order_page' => $order_page, 'csrf_token' => $csrf_token)) ?>'
                    href="<?php echo url_for('order_paid', array('id' => $id)) ?>">Yes</a>
                <a class="btn btn-primary" data-dismiss="modal">No</a>
            </div>
        </div>
    </div>
</div>