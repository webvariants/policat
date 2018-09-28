<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li  class="breadcrumb-item active">Actions</li>
  </ol>
</nav>
<div class="page-header">
  <h1>Actions</h1>
</div>
<?php include_component('d_action', 'list') ?>
<?php /* @var $sf_user myUser */
if ($sf_user->isAuthenticated() && $sf_user->getGuardUser()->hasCampaigns()):
  ?>
  <a class="btn btn-primary" href="<?php echo url_for('petition_new_') ?>">Start new e-action</a>
  <?php
 endif ?>
