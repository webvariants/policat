<?php include_partial('dashboard/admin_tabs', array('active' => 'users')) ?>
<?php include_component('user', 'list') ?>
<a class="btn" href="<?php echo url_for('user_new') ?>">New</a>
<a class="btn ajax_link post" href="<?php echo url_for('user_emails') ?>">Export e-mail-addresses</a>