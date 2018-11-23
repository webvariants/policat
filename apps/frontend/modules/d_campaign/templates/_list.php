<div id="campaing_list">
    <table class="table table-striped table-bordered">
        <thead>
        <th>Name</th>
        <th>DPO</th>
        <th>Status</th>
        <th>Billing</th>
        <th>Package</th>
        <th>Actions</th>
        <th></th>
        </thead>
        <tbody>
            <?php
            foreach ($campaigns as $campaign): /* @var $campaign Campaign */
              ?>
              <tr>
                  <td>
                      <a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a>
                  </td>
                  <td>
                      <?php if ($campaign->getDataOwnerId()): ?>
                        <a href="<?php echo url_for('user_edit', array('id' => $campaign->getDataOwnerId())) ?>"><?php echo $campaign->getDataOwner()->getFullName() ?></a>
                      <?php endif ?>
                  </td>
                  <td>
                      <?php if ($campaign->getPublicEnabled() == Campaign::PUBLIC_ENABLED_YES): ?>
                        community campaign<br />
                      <?php endif ?>
                      <?php if ($campaign->getStatus() == CampaignTable::STATUS_DELETED): ?>
                        soft deleted
                      <?php endif ?>
                  </td>
                  <td>
                      <?php if ($campaign->getBillingEnabled()): ?>
                        enabled
                      <?php else: ?>
                        free
                      <?php endif ?>
                  </td>
                  <td>
                      <?php if ($campaign->getBillingEnabled()): ?>
                        <?php if ($campaign->getQuotaId()): ?>
                          <?php echo $campaign->getQuota()->getPercent() ?>% used
                        <?php else: ?>
                          no package
                        <?php endif ?>
                      <?php endif ?>
                  </td>
                  <td><?php echo $campaign->getPetition()->count() ?></td>
                  <td>
                      <?php if ($campaign->getStatus() == CampaignTable::STATUS_DELETED): ?>
                        <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('campaign_hard_delete', array('id' => $campaign->getId())) ?>">wipe</a>
                      <?php endif ?>
                  </td>
              </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php include_partial('dashboard/pager', array('pager' => $campaigns)) ?>
</div>