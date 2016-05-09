<?php /* @var $form CampaignPublicEnableForm */ ?>
<div id="campaign_public">
  <form class="ajax_form clearfix" method="post" action="<?php echo url_for('campaign_public', array('id' => $campaign->getId())) ?>">
    <?php echo $form->renderHiddenFields() ?>
    <div class="clearfix bottom10"><?php echo $form['public_enabled']->renderRow() ?></div>
  </form>
</div>