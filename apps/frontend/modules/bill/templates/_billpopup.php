<?php use_helper('Number') ?>
<div class="modal hide hidden_remove modal-large" id="bill_pdf_modal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>Invoice <?php echo StoreTable::value(StoreTable::BILLING_PREFIX) . $id ?>
            <?php if ($bill->getVat()): ?>VAT-No: <?php echo $bill->getVat() ?><?php endif ?>
        </h3>
    </div>
    <div class="modal-body">
        <iframe width="840" height="1100" src="<?php echo url_for('bill_show', array('id' => $id)) ?>"></iframe>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary" href="<?php echo url_for('bill_show', array('id' => $id)) ?>?view=download">Download</a>
        <?php if ($bill->getUser()->getSwiftEmail()): ?>
          <a class="btn ajax_link" href="<?php echo url_for('bill_mail', array('id' => $bill->getId())) ?>">Mail to <?php echo $bill->getUser()->getEmailAddress() ?></a>
        <?php endif ?>
        <a class="btn" data-dismiss="modal">Close</a>
    </div>
</div>