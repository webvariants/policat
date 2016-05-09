<?php /* @var $form CampaignBillingForm */ ?>
<?php if ($form): ?>
  <div id="campaign_billing">
      <form class="ajax_form clearfix" method="post" action="<?php echo url_for('campaign_billing', array('id' => $campaign->getId())) ?>">
          <?php echo $form->renderHiddenFields() ?>
          <div class="clearfix bottom10"><?php echo $form['billing_enabled']->renderRow() ?></div>
      </form>
  </div>
<?php else: ?>
  <br />
<?php endif ?>