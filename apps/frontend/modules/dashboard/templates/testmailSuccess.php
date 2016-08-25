<?php include_partial('admin_tabs', array('active' => 'testmail')); ?>
<h2>Send a testmail</h2>
<form id="testmail" class="form-horizontal ajax_form" method="post" action="<?php echo url_for('admin_testmail') ?>">
  <?php echo $form ?>
  <div class="form-actions">
    <button class="btn btn-primary">Send</button>
  </div>
</form>

<?php if ($last_bounce): ?>
<label>Last bounce of testmails</label>
<code><?php echo $last_bounce ?></code>
<?php endif; ?>
