<?php
/* @var $signings policatPager */
use_helper('Number');
?>
<?php if (!isset($no_filter)): ?>
  <form class="form-inline ajax_form" action="<?php echo url_for($route, array_merge($route_params->getRawValue(), array('page' => 1))) ?>" method="get">
      <?php echo $form ?>
      <input type="hidden" name="s" value="<?php echo $subscriptions ? 1 : 0 ?>" />
      <button class="btn btn-primary" type="submit">Filter</button>
      <button class="filter_reset btn btn-small">Reset filter</button>
  </form>
<?php endif ?>
<div id="data">
    <div class="row">
        <?php if (isset($count)): ?><p class="span6">Count: <span class="label label-success"><?php echo format_number($count) ?></span></p><?php endif ?>
        <?php if (isset($pending)): ?>
          <p class="span6" style="text-align: right">
              Signings with pending verification (not shown): <span class="label label-important"><?php echo format_number($pending) ?></span>
              <?php if (isset($petition)): /* @var $petition Petition */ ?>
                Auto deleted: <span class="label label-warning"><?php echo format_number($petition->getDeletedPendings()) ?></span>
              <?php endif ?>
          </p>
        <?php endif ?>
    </div>
    <?php if (isset($signings) && $signings->count()): ?>
      <table class="table table-bordered">
          <thead>
              <tr>
                  <?php if ($show_petition): ?><th>Action</th><?php endif ?>
                  <th>Date</th>
                  <?php if ($show_status): ?><th>Status</th><?php endif ?>
                  <?php if ($show_email): ?>
                    <th>E-mail</th>
                    <th>Bounce</th>
                  <?php endif ?>
                      <?php if ($show_subscriber): ?><th>Subscriber</th><?php endif ?>
                  <th>Country</th>
                  <th>Name</th>
                  <?php if ($show_email): ?><th>Address</th><?php endif ?>
                  <?php if ($can_delete): ?><th></th><?php endif ?>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($signings as $signing): /* @var $signing PetitionSigning */ ?>
                <tr id="signing_row_<?php echo $signing->getId() ?>">
                    <?php if ($show_petition): ?><td><?php echo $signing->getPetition()->getName() ?></td><?php endif ?>
                    <td><?php echo $signing->getCreatedAt() ?></td>
                    <?php if ($show_status): ?><td><?php echo $signing->getStatusName() ?></td><?php endif ?>
                    <?php if ($show_email): ?>
                      <td>
                          <?php echo $signing->getEmailScramble() ?>
                          <?php if ($signing->getVerified() == PetitionSigning::VERIFIED_YES): ?><span class="label label-success">verified</span><?php endif ?>
                          <?php if ($signing->getVerified() == PetitionSigning::VERIFIED_NO): ?><span class="label label-warning">not verified</span><?php endif ?>
                      </td>
                      <td>
                          <?php if ($signing->getBounce()): ?>
                            <?php echo $signing->getBounceAt() ?>
                            <?php if ($signing->getBounceBlocked()): ?><span class="label label-warning">blocked</span><?php endif ?>
                            <?php if ($signing->getBounceBlocked()): ?><span title="hard bounce" class="label label-important">hard</span><?php endif ?>
                            <br />
                            <code title="bounce error"><?php echo $signing->getBounceError() ?></code>
                            <code title="bounce error related to"><?php echo $signing->getBounceRelatedTo() ?></code>
                          <?php endif ?>
                      </td>
                    <?php endif ?>
                    <?php if ($show_subscriber): ?>
                      <td>
                          <?php echo $signing->getSubscribe() ? 'yes' : 'no' ?>
                          <?php if ($signing->getWidget()->getUserId() && $signing->getWidget()->getDataOwner() == WidgetTable::DATA_OWNER_YES): ?><br /><span class="label label-info">Data-owner</span><?php endif ?>
                          <?php if ($signing->getVerified() == PetitionSigning::VERIFIED_YES): ?><span class="label label-success">verified</span><?php endif ?>
                          <?php if ($signing->getVerified() == PetitionSigning::VERIFIED_NO): ?><span class="label label-warning">not verified</span><?php endif ?>
                      </td>
                    <?php endif ?>
                    <td><?php echo $signing->getCountry() ?></td>
                    <td><?php echo $signing->getComputedName() ?></td>
                    <?php if ($show_email): ?><td><?php echo $signing->getComputedAddress('en', ", ") ?></td><?php endif ?>
                    <?php if ($can_delete): ?><td><a class="btn btn-mini ajax_link" href="<?php echo url_for('data_delete', array('id' => $signing->getId())) ?>">delete</a></td><?php endif ?>
                </tr>
              <?php endforeach ?>
          </tbody>
      </table>
      <?php include_partial('dashboard/pager', array('pager' => $signings)) ?>
      <?php if (isset($count)): ?>
        <div class="well">
            <?php if (isset($download_url)): ?>
              Download data (utf-8 encoded .csv):
              <a class="btn btn-mini ajax_link post" href="<?php echo $download_url ?>">Download</a>
            <?php endif ?>
            <p class="top15 bottom0">
                The participant list exports contain hashes to compare or deduplicate signings with other lists. Input: e-mail address, utf-8 encoded, white spaces
                removed front and tail, all letters converted to lowercase. Function: bcrypt, parameters cost=10 and salt='POLICAT1234567890ABCDE'
            </p>
        </div>
      <?php endif ?>
    <?php else: ?>
      <p>No signings yet.</p>
    <?php endif ?>
</div>