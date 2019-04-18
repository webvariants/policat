<?php
/* @var $signings policatPager */
/* @var $petition Petition */
use_helper('Number');
?>

<div id="data">
    <p>
        Bounces pending review (see list below): <span class="label label-info"><?php echo format_number($signings->getNbResults()) ?></span>
        Deleted bounces (manually and auto deleted): <span class="label label-info"><?php echo format_number($petition->getDeletedHardBounces() + $petition->getDeletedBouncesManually()) ?></span>
    </p>
    <?php if (isset($signings) && $signings->count()): ?>
    <form class="ajax_form" method="post" action="<?php echo url_for('petition_bounce_delete', array('id' => $petition->getId())) ?>">
          <input type="hidden" name="csrf_token" value="<?php echo $delete_token ?>">
          <table class="table table-responsive-md table-bordered">
              <thead>
                  <tr>
                      <th><label class="form-inline"><input type="checkbox" class="checkbox-all" data-target=".bounce-id" /> <span>All</span></label></th>
                      <th>Bounce At</th>
                      <th>Status</th>
                      <th>E-mail</th>
                      <th>Subscriber</th>
                      <th>Blocked</th>
                      <th>Hard bounce</th>
                      <th>Reason</th>
                      <th>Related to</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($signings as $signing): /* @var $signing PetitionSigning */ ?>
                    <tr id="signing_row_<?php echo $signing->getId() ?>">
                        <td><input class="bounce-id" type="checkbox" name="ids[]" value="<?php echo $signing->getId() ?>"/></td>
                        <td><?php echo $signing->getBounceAt() ?></td>
                        <td><?php echo $signing->getStatusName() ?></td>
                        <td><?php echo $signing->getEmailScramble() ?></td>
                        <td><?php echo $signing->getSubscribe() ? 'yes' : 'no' ?></td>
                        <td><?php echo $signing->getBounceBlocked() ? 'yes' : 'no' ?></td>
                        <td><?php echo $signing->getBounceHard() ? 'yes' : 'no' ?></td>
                        <td><?php if ($signing->getBounceError()): ?><?php echo $signing->getBounceError() ?><?php else: ?><i>unknown</i><?php endif ?></td>
                        <td><?php echo $signing->getBounceRelatedTo() ?></td>
                    </tr>
                  <?php endforeach ?>
              </tbody>
              <tbody>
                  <tr>
                      <td colspan="10"><button type="submit" class="btn btn-default">Delete selected</button></td>
                  </tr>
              </tbody>
          </table>
      </form>
      <?php include_partial('dashboard/pager', array('pager' => $signings)) ?>
    <?php else: ?>
      <p>No bounces.</p>
    <?php endif ?>
</div>