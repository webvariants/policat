<?php use_helper('Date', 'Number'); ?>
<?php if ($quotas->count()): ?>
  <table class="table table-bordered">
      <tr><th>E-mail package</th><th class="span2">Start - End</th><th class="span2">Status</th><th class="span1"></th></tr>
      <?php foreach ($quotas as $quota): /* @var $quota Quota */ ?>
        <tr>
            <td>
                <?php echo $quota->getName() ?><br />
                <div class="progress"><div class="progress-bar" role="progressbar" style="width: <?php echo $quota->getPercent() ?>%" aria-valuenow="<?php echo $quota->getPercent() ?>" aria-valuemin="0" aria-valuemax="100"></div></div>
                <small><?php echo format_number($quota->getEmailsRemaining()) ?> remaining</small>
            </td>
            <td>
              <?php echo format_date($quota->getStartAt(), 'yyyy-MM-dd') ?><br /><?php echo format_date($quota->getEndAt(), 'yyyy-MM-dd') ?>
            </td>
            <td>
              <?php echo $quota->getStatusName() ?>
            </td>
            <td>
                <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
                  <a class="btn btn-secondary btn-sm" href="<?php echo url_for('quota_edit', array('id' => $quota->getId())) ?>">edit</a>
                <?php endif ?>
                <?php if ($quota->getOrderId() && ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN) || $sf_user->getUserId() == $quota->getUserId())): ?>
                  <a class="btn btn-secondary btn-sm" href="<?php echo url_for('order_show', array('id' => $quota->getOrderId())) ?>">order</a>
                <?php endif ?>
            </td>
        </tr>
      <?php endforeach ?>
  </table>
  <?php
else:
  if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN) || $campaign->getBillingEnabled()):
    ?>
    <p>This campaign has no e-mail packages yet.</p>
  <?php endif; ?>
<?php endif; ?>