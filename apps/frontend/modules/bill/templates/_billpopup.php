<?php use_helper('Number') ?>
<div class="modal hide hidden_remove" id="bill_pdf_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice <?php echo StoreTable::value(StoreTable::BILLING_PREFIX) . $id ?>
                    <?php if ($bill->getVat()): ?>VAT-No: <?php echo $bill->getVat() ?><?php endif ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-body-full-iframe">
                <iframe width="100%" height="1050" src="<?php echo url_for('bill_show', array('id' => $id)) ?>"></iframe>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="<?php echo url_for('bill_show', array('id' => $id)) ?>?view=download">Download</a>
                <?php if ($bill->getUser()->getSwiftEmail()): ?>
                  <a class="btn ajax_link" href="<?php echo url_for('bill_mail', array('id' => $bill->getId())) ?>">Mail to <?php echo $bill->getUser()->getEmailAddress() ?></a>
                <?php endif ?>
                <a class="btn" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>
