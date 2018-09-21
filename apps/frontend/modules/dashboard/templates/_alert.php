<div class="alert<?php if (isset($type)) echo ' alert-' . $type; else echo ' alert-info' ?>" role="alert">
  <a class="close" data-dismiss="alert">&times;</a>
  <?php if (isset($raw) && $raw): ?>
    <strong><?php echo $sf_data->getRaw('heading') ?></strong> <?php echo $sf_data->getRaw('message') ?>
  <?php else: ?>
    <strong><?php echo $heading ?></strong> <?php echo $message ?>
  <?php endif ?>
</div>
