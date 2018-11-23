<?php
$guard = $sf_user->getGuardUser()->getRawValue();
/* @var $guard sfGuardUser */
?>
<div id="target_list">
  <table class="table table-bordered table-striped">
    <thead><tr><th>Name</th><th>Status</th><th>Your rights</th><th></th></tr></thead>
    <tbody>
      <?php
      foreach ($target_lists as $target_list): /* @var $target_list MailingList */
        $tr = $guard->getTargetListRights($target_list->getRawValue()); /* @var $tr TargetListRights */
        ?>
        <tr>
          <td><?php echo $target_list->getName() ?></td>
          <td><?php echo $target_list->getStatusName() ?></td>
          <td>
            <?php if ($tr): ?>
              <?php if ($tr->getActive()): ?>
                <span class="label label-info">editor</span>
              <?php else: ?>
                <span class="label">disabled</span>
              <?php endif ?>
            <?php endif ?>
          </td>
          <td>
            <?php if ($guard->isTargetListMember($target_list->getRawValue(), true)): ?>
            <a class="btn btn-sm" href="<?php echo url_for('target_edit', array('id' => $target_list->getId())) ?>">edit</a>
            <a class="btn btn-sm ajax_link" href="<?php echo url_for('target_copy', array('id' => $target_list->getId())) ?>">copy</a>
            <?php endif ?>
            <?php if (!$tr || !$tr->getActive()): ?>
              <a class="btn btn-sm ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token_join, 'id' => $target_list->getId())) ?>' href="<?php echo url_for('target_join') ?>">join</a>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>