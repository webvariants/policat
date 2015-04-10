<?php include_partial('admin_tabs', array('active' => 'stats')); ?>
<?php use_helper('Number') ?>

<div class="form-horizontal">
  <fieldset>
    <label>Email-to-list actions</label>
    <div class="control-group">
      <label class="control-label">Mails sent</label>
      <div class="controls"><span class="widget_text"><?php echo format_number($sent) ?></span></div>
    </div>
    <div class="control-group">
      <label class="control-label">Mails in Sending Queue</label>
      <div class="controls"><span class="widget_text"><?php echo format_number($outgoing) ?></span></div>
    </div>
    <div class="control-group">
      <label class="control-label">Mails pending</label>
      <div class="controls"><span class="widget_text"><?php echo format_number($pending) ?></span></div>
    </div>
  </fieldset>
</div>