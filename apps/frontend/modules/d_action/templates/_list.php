<?php
$user = $sf_user->getGuardUser()->getRawValue(); /* @var $user sfGuardUser */
use_helper('Text', 'Number', 'Date');

if (!isset($no_filter)):
  /* @var $campaign Campaign */
  $url = isset($campaign) ? url_for('petition_pager', array('page' => 1, 'id' => $campaign->getId())) : url_for('petition_pager_all', array('page' => 1))
  ?>
  <form method="get" class="form-inline ajax_form filter_form" action="<?php echo $url ?>">
    <?php echo $form ?>
    <button class="btn btn-primary top15" type="submit">Filter</button>
    <button class="filter_reset btn btn-small top15">Reset filter</button>
  </form>
<?php endif ?>
<div id="action_list">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>E-action
          <a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_ACTION_ASC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_ACTION_ASC ?>">&darr;</a><?php
?><a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_ACTION_DESC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_ACTION_DESC ?>">&uarr;</a>

          <span class="pull-right">ID<?php
?><a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_ID_ASC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_ID_ASC ?>">&darr;</a><?php
?><a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_ID_DESC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_ID_DESC ?>">&uarr;</a>
          </span>
        </th>
        <?php if (!isset($campaign)): ?>
          <th>in&nbsp;campaign&nbsp;<a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_CAMPAIGN_ASC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_CAMPAIGN_ASC ?>">&darr;</a><?php ?><a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_CAMPAIGN_DESC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_CAMPAIGN_DESC ?>">&uarr;</a>
          </th>
        <?php endif ?>
        <th>Status
          <a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_STATUS_ASC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_STATUS_ASC ?>">&darr;</a><?php
        ?><a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_STATUS_DESC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_STATUS_DESC ?>">&uarr;</a>
        </th>
        <th>Type</th>
        <th>Last activity
          <a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_ACTIVITY_ASC) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_ACTIVITY_ASC ?>">&darr;</a><?php
        ?><a class="filter_order <?php if ($form->getValue(PetitionTable::FILTER_ORDER) == PetitionTable::ORDER_ACTIVITY_DESC || !$form->getValue(PetitionTable::FILTER_ORDER)) echo ' active' ?>" data-value="<?php echo PetitionTable::ORDER_ACTIVITY_DESC ?>">&uarr;</a>
        </th>
        <th class="rotate"><div>Participants</div></th>
    <th class="rotate"><div><span title="Signings with verification pending">Pending</span></div></th>
    <th class="rotate"><div>Widgets</div></th>
    <th>Your rights</th>
    <th></th>
    </tr>
    </thead>
    <tbody>
      <?php
      foreach ($petitions as $petition):
        /* @var $petition Petition */
        $pr = $user->getRightsByPetition($petition->getRawValue());
        $cr = $user->getRightsByCampaign($petition->getRawValue()->getCampaign());
        $public_campaign = $petition->getRawValue()->getCampaign()->getPublicEnabled();
        ?>
        <tr>
          <td><?php $sf_user->linkPetition($petition, 20) ?></td>
          <?php if (!isset($campaign)): ?><td><?php $sf_user->linkCampaign($petition->getCampaign(), 15) ?></td><?php endif ?>
          <td><?php echo $petition->getStatusName() ?></td>
          <td><?php echo $petition->getKindName() ?></td>
          <td><?php echo format_date($petition->getActivityAt(), 'yyyy-MM-dd') ?></td>
          <td class="align-right"><?php echo format_number($petition->countSignings()) ?></td>
          <td class="align-right"><?php echo format_number($petition->countSigningsPending()) ?></td>
          <td class="align-right"><?php echo format_number($petition->countWidgets()) ?></td>
          <td>
            <?php $x = 1; ?>
            <?php if ($cr && $cr->getActive() && $cr->getAdmin()): $x = 0 ?><span class="label label-important">admin</span><?php endif ?>
            <?php if ($x && $pr && $pr->getActive() && $pr->getMember()): $x = 0 ?><span class="label label-info">editor</span><?php endif ?>
            <?php if ($x && !$public_campaign && (!$cr || !$cr->getActive() || !($cr->getMember() || $cr->getAdmin()))): ?>not&nbsp;campaign&nbsp;member<?php endif ?>
          </td>
          <td>
            <?php if ($petition->isEditableBy($user)): ?>
              <a class="btn btn-mini btn-primary" href="<?php echo url_for('petition_edit_', array('id' => $petition->getId())) ?>">edit</a>
            <?php endif ?>
            <?php if ($user->isPetitionMember($petition->getRawValue(), true)): ?>
              <a class="btn btn-mini" href="<?php echo url_for('petition_data', array('id' => $petition->getId())) ?>">Signings</a>
            <?php endif ?>
            <?php if (!$user->isCampaignAdmin(isset($campaign) ? $campaign->getRawValue() : $petition->getCampaignId())): ?>
              <?php if ($pr && $pr->getActive() && ($pr->getMember() || $pr->getAdmin())): ?>
                <a class="btn btn-mini ajax_link post" data-submit='<?php echo json_encode(array('campaign' => isset($campaign) ? 1 : 0, 'csrf_token' => $csrf_token_leave, 'id' => $petition->getId())) ?>' href="<?php echo url_for('action_leave') ?>">leave</a>
              <?php else: ?>
                <a class="btn btn-mini ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token_join, 'id' => $petition->getId())) ?>' href="<?php echo url_for('action_join') ?>">join</a>
              <?php endif ?>
            <?php endif ?>
            <?php if ($petition->getCampaign()->getDataOwnerId() == $sf_user->getUserId() || $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <a class="btn btn-danger btn-mini ajax_link" href="<?php echo url_for('petition_delete_', array('id' => $petition->getId())) ?>">Delete</a>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  <?php include_partial('dashboard/pager', array('pager' => $petitions)) ?>
</div>
