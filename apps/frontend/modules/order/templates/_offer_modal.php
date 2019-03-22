<?php use_helper('Number') ?>
<div class="modal hide hidden_remove modal-large" id="offer_pdf_modal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>Offer</h3>
    </div>
    <div class="modal-body modal-body-full-iframe">
        <iframe width="840" height="1100" src="<?php echo url_for('order_offer', array('id' => $id)) ?>"></iframe>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary" href="<?php echo url_for('order_offer', array('id' => $id)) ?>?view=download">Download</a>
        <a class="btn btn-secondary" data-dismiss="modal">Close</a>
    </div>
</div>
