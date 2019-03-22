<?php if (isset($campaign_rights_list)): ?>
  <div class="row">
      <div class="col-md-6">
          <h2>Campaigns</h2>
          <?php if ($campaign_rights_list->count()): ?>
            <table class="table table-striped table-bordered ">
                <thead><tr><th>Name</th><th>Rights</th></tr></thead>
                <tbody>
                    <?php foreach ($campaign_rights_list as $campaign_rights): /* @var $campaign_rights CampaignRights */ ?>
                      <tr>
                          <td><a href="<?php echo url_for('campaign_edit_', array('id' => $campaign_rights->getCampaignId())) ?>"><?php echo $campaign_rights->getCampaign()->getName() ?></a></td>
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
          <?php if (isset($join_form)): ?>
            <form id="my_campaigns_join" class="ajax_form form-inline" action="<?php echo url_for('campaign_join') ?>" method="post">
                <?php echo $join_form ?>
                <button class="btn btn-small button-medium">join</button>
            </form>
          <?php endif ?>
      </div>
      <div class="col-md-6">
          <h2>Actions</h2>
          <?php if ($petition_rights_list->count()): ?>
            <table class="table table-striped table-bordered ">
                <thead><tr><th>Name</th><th>Rights</th></tr></thead>
                <tbody>
                    <?php foreach ($petition_rights_list as $petition_rights): /* @var $petition_rights PetitionRights */ ?>
                      <tr>
                          <td><a href="<?php echo url_for('petition_overview', array('id' => $petition_rights->getPetitionId())) ?>"><?php echo $petition_rights->getPetition()->getName() ?></a></td>
                          <td>
                              <?php if (!$petition_rights->getActive()): ?><span class="label label-info">disabled</span><?php endif ?>
                              <?php if ($petition_rights->getMember()): ?><span class="label">Editor</span><?php endif ?>
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
<?php endif; ?>
