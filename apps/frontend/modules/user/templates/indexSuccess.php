<?php include_partial('dashboard/admin_tabs', array('active' => 'users')) ?>
<?php include_component('user', 'list') ?>
<a class="btn" href="<?php echo url_for('user_new') ?>">New</a>