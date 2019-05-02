<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li  class="breadcrumb-item active">Dashboard</li>
  </ul>
</nav>
<div class="page-header">
  <h1>Dashboard</h1>
</div>
<div class="row">
  <div class="col-md-8">
      <?php if ($sf_user->isNotBlocked()): ?>
        <?php if ($no_campaign): ?>
            <p class="alert alert-danger">To create a new e-action you have to be member of a campaign.<br />Please create or join a campaign first.</p>
        <?php endif ?>
        <?php include_component('ticket', 'todo') ?>
        <?php include_component('dashboard', 'trending') ?>
      <?php else: ?>
        Your account has been blocked. To apply to get unblocked click <a class="ajax_link" href="<?php echo url_for('unblock') ?>">here</a>
      <?php endif ?>
  </div>
  <?php if ($sf_user->isNotBlocked()): ?>
    <div class="col-md-4">
        <?php include_component('d_campaign', 'myCampaigns'); ?>
    </div>
  <?php endif ?>
</div>
