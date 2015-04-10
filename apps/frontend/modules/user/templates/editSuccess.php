<?php include_partial('dashboard/admin_tabs', array('active' => 'users')) ?>
<div id="user_form">
  <?php
  $is_new = $form->getObject()->isNew();
  $action = $is_new ? url_for('user_new') : url_for('user_edit', array('id' => $form->getObject()->getId()))
  ?>
  <form class="ajax_form form-horizontal" action="<?php echo $action ?>" method="post" autocomplete="off">
    <fieldset>
      <legend class="pull-left">Profile</legend>
      <div class="row">
        <div class="span6">
          <?php echo $form->renderHiddenFields() ?>
          <?php
          echo $form->renderRows('email_address');
          if (!$is_new) echo $form->renderRows('password', 'password_again');
          echo $form->renderRows('first_name', 'last_name', 'phone', 'language_id') ?>
          <?php if ($is_new): ?>
          <p>An E-mail will be sent to the user to activate and set the password.</p>
          <?php endif ?>        
        </div>
        <div class="span6">
          <?php echo $form->renderRows('organisation', 'website', 'street', 'post_code', 'city', 'country', 'mobile') ?>
        </div>
      </div>
    </fieldset>
    <fieldset>
      <legend class="pull-left">Admin only settings</legend>
      <div class="row">
        <div class="span6">
          <?php if (!$is_new) echo $form->renderRows('is_active') ?>
        </div>
        <div class="span6">
          <?php echo $form->renderRows('groups_list') ?>
        </div>
      </div>
    </fieldset>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Save</button>
      <a class="btn" href="<?php echo url_for('user_idx') ?>">Cancel</a>
      <?php if (!$form->getObject()->isNew()): $user = $form->getObject(); /* @var $user sfGuardUser */?>
        <?php if (!$user->hasPermission(myUser::CREDENTIAL_ADMIN)): ?>
          <?php if ($user->hasPermission(myUser::CREDENTIAL_USER)): ?>
            <a class="btn btn-danger ajax_link" href="<?php echo url_for('user_block', array('id' => $user->getId())) ?>">Block</a>
          <?php else: ?>
            <a class="btn ajax_link" href="<?php echo url_for('user_unblock', array('id' => $user->getId())) ?>">Unblock</a>
          <?php endif ?>
        <?php endif ?>
      <div class="pull-right">
        <a class="btn btn-danger ajax_link" href="<?php echo url_for('user_delete', array('id' => $form->getObject()->getId())) ?>">Delete</a>
      </div>
      <?php endif ?>
    </div>
  </form>
</div>
<?php if (isset($campaign_rights_list)): ?>
  <div class="row">
    <div class="span6">
      <h2>Campaigns</h2>
      <?php if ($campaign_rights_list->count()): ?>
        <table class="table table-striped table-bordered ">
          <thead><tr><th>Name</th><th>Rights</th></tr></thead>
          <tbody>
            <?php foreach ($campaign_rights_list as $campaign_rights): /* @var $campaign_rights CampaignRights */ ?>
              <tr>
                <td><?php echo $campaign_rights->getCampaign()->getName() ?></td>
                <td>
                  <?php if (!$campaign_rights->getActive()): ?><span class="label label-info">disabled</span><?php endif ?>
                  <?php if ($campaign_rights->getMember()): ?><span class="label">member</span><?php endif ?>
                  <?php if ($campaign_rights->getAdmin()): ?><span class="label label-important">admin</span><?php endif ?>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>None yet.</p>
      <?php endif ?>
    </div>
    <div class="span6">
      <h2>Actions</h2>
      <?php if ($petition_rights_list->count()): ?>
        <table class="table table-striped table-bordered ">
          <thead><tr><th>Name</th><th>Rights</th></tr></thead>
          <tbody>
            <?php foreach ($petition_rights_list as $petition_rights): /* @var $petition_rights PetitionRights */ ?>
              <tr>
                <td><?php echo $petition_rights->getPetition()->getName() ?></td>
                <td>
                  <?php if (!$petition_rights->getActive()): ?><span class="label label-info">disabled</span><?php endif ?>
                  <?php if ($petition_rights->getMember()): ?><span class="label">member</span><?php endif ?>
                  <?php if ($petition_rights->getAdmin()): ?><span class="label label-important">member-manager</span><?php endif ?>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>None yet.</p>
      <?php endif ?>
    </div>
  </div>
  <p><a class="btn" href="<?php echo url_for('user_idx') ?>">Back</a></p>
<?php endif; ?>
