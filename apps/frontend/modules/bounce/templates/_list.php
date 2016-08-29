<?php
/* @var $signings policatPager */
use_helper('Number');
?>

<div id="data">
    <div class="row">
        <p class="span6">Total bounces: <span class="label label-warning"><?php echo format_number($signings->getNbResults()) ?></span></p>
    </div>
    <?php if (isset($signings) && $signings->count()): ?>
      <table class="table table-bordered">
          <thead>
              <tr>
                  <th>Bounce At</th>
                  <th>Status</th>
                  <th>E-mail</th>
                  <th>Subscriber</th>
                  <th>Blocked</th>
                  <th>Hard bounce</th>
                  <th>Reason</th>
                  <th>Related to</th>
                  <th></th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($signings as $signing): /* @var $signing PetitionSigning */ ?>
                <tr id="signing_row_<?php echo $signing->getId() ?>">
                    <td><?php echo $signing->getBounceAt() ?></td>
                    <td><?php echo $signing->getStatusName() ?></td>
                    <td><?php echo $signing->getEmailScramble() ?></td>
                    <td><?php echo $signing->getSubscribe() ? 'yes' : 'no' ?></td>
                    <td><?php echo $signing->getBounceBlocked() ? 'yes' : 'no' ?></td>
                    <td><?php echo $signing->getBounceHard() ? 'yes' : 'no' ?></td>
                    <td><?php echo $signing->getBounceError() ?></td>
                    <td><?php echo $signing->getBounceRelatedTo() ?></td>
                    <td><a class="btn btn-mini ajax_link" href="<?php echo url_for('data_delete', array('id' => $signing->getId())) ?>">delete</a></td>
                </tr>
              <?php endforeach ?>
          </tbody>
      </table>
      <?php include_partial('dashboard/pager', array('pager' => $signings)) ?>
    <?php else: ?>
      <p>No bounces.</p>
    <?php endif ?>
</div>