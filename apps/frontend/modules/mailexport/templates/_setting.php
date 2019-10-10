<?php if ($show): ?>
  <hr />
  <h3 class="mt-4">Mail export settings</h3>
  <form id="mailexport_setting_form" class="ajax_form form-horizontal" action="<?php echo url_for('mailexport_setting', array('id' => $petition->getId())) ?>" method="post">
    <?php echo $form ?>
    <div class="form-actions">
          <button class="btn btn-primary">Save</button>
          <a class="btn btn-secondary" href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>" >Cancel</a>
      </div>
  </form>

  <?php if (count($enabled_services)): ?>
  <h5 class="mt-4">Test mail export service credentials</h5>
  <?php foreach ($enabled_services as $name => $serviceName) ?>
  <div id="test-<?php echo $name ?>" class="mb-4">
    <a class="btn btn-secondary ajax_link post" data-submit='<?php echo json_encode(array('service' => $name, 'csrf_token' => $test_csrf_token)) ?>' href="<?php echo url_for('mailexport_test', array('id' => $petition->getId())) ?>"><?php echo $serviceName ?></a>
  </div>
  <?php endif ?>
<?php endif ?>