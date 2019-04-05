<?php use_helper('Number') ?>
<div class="modal hide hidden_remove" id="offer_pdf_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Offer/h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-body-full-iframe">
                <iframe width="840" height="1100"
                    src="<?php echo url_for('order_offer', array('id' => $id)) ?>"></iframe>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary"
                    href="<?php echo url_for('order_offer', array('id' => $id)) ?>?view=download">Download</a>
                <a class="btn btn-secondary" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>