<?php
if ($billingEnabled) {
  include_component('order', 'notice', array('campaign' => $campaign));
}
if ($follows):
  /* @var $follows Petition */
  ?>
  <div class="alert alert-info" id="alert-forward-info">
      <a class="close" data-dismiss="alert">&times;</a>
      Some or all widget-URLs currently forward to action <a href="<?php echo url_for('petition_overview', array('id' => $follows->getId())) ?>"><?php echo $follows->getName() ?></a>
      (<a href="<?php echo url_for('petition_edit_', array('id' => $petition->getId())) ?>">edit here</a>).
  </div>
  <?php
endif;

if ($petition_draft):
  ?>
  <div class="alert alert-info">
      <a class="close" data-dismiss="alert">&times;</a>
      Note: Your action is in 'draft' status. Widgets are invisible. To activate your action, set status to 'active'.
  </div>
  <?php
endif;

if ($petition_text_draft):
  ?>
  <div class="alert alert-info">
      <a class="close" data-dismiss="alert">&times;</a>
      Note: A translation is in 'draft' status. To activate a language, set translation status to 'active'.
  </div>
<?php endif;
?>