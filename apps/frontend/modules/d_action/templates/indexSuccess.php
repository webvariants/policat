<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li class="active">Actions</li>
</ul>
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