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
    <?php if ($sf_user->isAuthenticated()): ?>
      <?php if ($sf_user->isNotBlocked()): ?>
        <?php include_component('ticket', 'todo') ?>
        <?php include_component('dashboard', 'trending') ?>
      <?php else: ?>
        Your account has been blocked. To apply to get unblocked click <a class="ajax_link" href="<?php echo url_for('unblock') ?>">here</a>
      <?php endif ?>
    <?php else: ?>
      <p>Please <a rel="nofollow" data-toggle="modal" href="#login_modal" href="<?php echo url_for('ajax_signin') ?>">login</a>.</p>
    <?php endif ?>
  </div>
  <?php if ($sf_user->isNotBlocked()): ?>
    <div class="col-md-4">
        <?php include_component('d_campaign', 'myCampaigns'); ?>
    </div>

    <?php if ($no_campaign): ?>
        <div class="modal hide modal_show hidden_remove">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alert</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>To create a new e-action you have to be member of a campaign.<br />Please create or join a campaign first.</p>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-primary" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
  <?php endif ?>
</div>
