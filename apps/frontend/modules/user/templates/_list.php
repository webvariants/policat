<?php if (!isset($no_filter)):
?>
  <form method="get" class="form-inline ajax_form filter_form mb-2" action="<?php echo url_for('user_pager', array('page' => 1)) ?>">
  <?php echo $form ?>
    <button class="btn btn-primary btn-sm mt-3" type="submit">Filter</button>
    <button class="filter_reset btn btn-secondary btn-sm mt-3">Reset filter</button>
  </form>
<?php endif ?>
<div id="user_list">
  <table class="table table-responsive-md table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>E-mail</th>
        <th title="Active - User can login. Blocked - User can only request to get unblocked when logged in.">Active</th>
        <th>Last login</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): /* @var $user sfGuardUser */ ?>
        <tr>
          <td><?php echo $user->getFullName() ?></td>
          <td><?php echo $user->getEmailAddress() ?></td>
          <td>
            <?php echo $user->getIsActive() ? 'yes' : 'no'  ?>
            <?php if ($user->hasPermission(myUser::CREDENTIAL_ADMIN)): ?><span class="label">admin</span><?php endif ?>
            <?php if (!$user->hasPermission(myUser::CREDENTIAL_USER)): ?><span class="label label-important">blocked</span><?php endif ?>
          </td>
          <td><?php echo $user->getLastLogin() ?></td>
          <td>
            <a class="btn btn-primary btn-sm" href="<?php echo url_for('user_edit', array('id' => $user->getId())) ?>">edit</a>
            <?php if (!$user->hasPermission(myUser::CREDENTIAL_ADMIN)): ?>
              <?php if ($user->hasPermission(myUser::CREDENTIAL_USER)): ?>
                <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('user_block', array('id' => $user->getId())) ?>">block</a>
              <?php else: ?>
                <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('user_unblock', array('id' => $user->getId())) ?>">unblock</a>
              <?php endif ?>
              <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('user_delete', array('id' => $user->getId())) ?>">delete</a>
              <a title="login as this user" class="btn btn-danger btn-sm ajax_link post" href="<?php echo url_for('user_switch', array('id' => $user->getId())) ?>">switch</a>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  <?php include_partial('dashboard/pager', array('pager' => $users)) ?>
</div>
