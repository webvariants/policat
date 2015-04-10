<?php decorate_with(dirname(__FILE__).'/../../../templates/dashboard.php') ?>

<div class="page-header">
  <h1><?php echo isset($head) ? $head : 'Not found' ?></h1>
</div>
<p>
  <?php echo isset($message) ? $message : 'Not found.' ?>
</p>