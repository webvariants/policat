<?php /* @var $form CampaignSwitchesForm */ ?>
<div id="campaign_switches">
  <form class="ajax_form clearfix" method="post" action="<?php echo url_for('campaign_switches', array('id' => $campaign->getId())) ?>">
    <?php echo $form->renderHiddenFields() ?>
    <div class="clearfix bottom10"><?php echo $form['owner_register']->renderRow() ?></div>
    <div class="clearfix bottom10"><?php echo $form['join_enabled']->renderRow() ?></div>
  </form>
</div>