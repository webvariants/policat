<?php if ($no_target_list): ?>
  <p>Set a target list first.</p>
  <?php
else:
  if ($pledges instanceof sfOutputEscaperArrayDecorator)
    $pledges = $pledges->getRawValue();
  ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
      <li class="breadcrumb-item"><a href="<?php echo url_for('pledge_list', array('id' => $petition->getId())) ?>">Pledges</a></li>
      <li class="breadcrumb-item active"><?php // echo $pledge_item->getName()     ?></li>
    </ol>
  </nav>
  <?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
  <?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'pledge_stats')) ?>
  <form method="get" class="form-inline ajax_form filter_form mb-2" action="<?php echo url_for('pledge_stats_pager', array('page' => 1, 'id' => $petition->getId())) ?>">
    <?php echo $form ?>
    <button class="btn btn-primary btn-sm mt-3" type="submit">Filter</button>
    <button class="filter_reset btn btn-secondary btn-sm mt-3">Reset filter</button>
  </form>
  <?php
  include_partial('contacts', array(
      'contacts' => $contacts,
      'petition_id' => $petition->getId(),
      'active_pledge_item_ids' => $active_pledge_item_ids,
      'pledges' => $pledges,
      'pledge_items' => $pledge_items
  ))
  ?>
<?php endif;
