<?php use_helper('Number') ?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
    <li class="breadcrumb-item active">Overview</li>
  </ol>
</nav>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'overview')) ?>
<div class="row">
  <div class="col-md-8">
    <div class="form-horizontal">
      <table class="table table-responsive-md table-sm table-bordered">
        <tr>
          <th>Input data</th>
          <td>
              <?php echo Petition::$NAMETYPE_SHOW[$petition->getNametype()] ?>, <?php
              if ($petition->getWithAddress()) echo Petition::$WITH_ADDRESS_SHOW[$petition->getWithAddress()] . ', ';
              if ($petition->getWithExtra1() == Petition::WITH_EXTRA_YES) echo 'free text field, ';
              if ($petition->getWithCountry()): ?>country<?php else: ?> without country<?php endif;
              if ($petition->getWithComments()): ?>, comments<?php endif ?>
          </td>
        </tr>
        <tr>
          <th>Status</th>
          <td><?php echo $petition->getStatusName() ?></td>
        <?php if ($petition->getFollowPetitionId()): ?>
        <tr>
          <th>Follow-up action</th>
          <td><?php echo $petition->getFollowPetition()->getName() ?></td>
        <?php endif ?>
        <?php if ($petition->isGeoKind()): ?>
        <tr>
          <th>Mails sent</th>
          <td><?php echo format_number($petition->countMailsSent()) ?></td>
        </tr>
        <tr>
          <th>Mails in Sending Queue</th>
          <td><?php echo format_number($petition->countMailsOutgoing()) ?></td>
        </tr>
        <tr>
          <th>Mails pending</th>
          <td><?php echo format_number($petition->countMailsPending()) ?></span></div>
        </div>
        <?php endif ?>
        <tr>
          <th>Signings via widgets</th>
          <td><?php echo format_number($petition->countSignings(60)) ?></td>
        </tr>
        <tr>
          <th>Signings via API</th>
          <td><?php echo format_number($petition->sumApi(60)) ?></td>
        </tr>
        <tr>
          <th>Manual counter tweak</th>
          <td><?php echo format_number($petition->getAddnum()) ?></td>
        </tr>
        <tr>
          <th>Signings total</th>
          <td><?php echo format_number($petition->countSigningsPlusApi(60)) ?></td>
        </tr>
        <tr>
          <th>Signings last 24h</th>
          <td><?php echo format_number($petition->countSignings24()) ?></td>
        </tr>
        <tr>
          <th>Signings with verification pending</th>
          <td><?php echo format_number($petition->countSigningsPending()) ?></td>
        </tr>
        <tr>
          <th>Widgets</th>
          <td><?php echo format_number($petition->countWidgets()) ?></td>
        </tr>
      </table>
      <a href="<?php echo url_for('api_v2_doc') ?>" target="_blank">API documentation</a>
    </div>
  </div>
  <div class="col-md-4">
      <?php include_component('d_action', 'members', array('petition' => $petition, 'no_admin' => false)) ?>
      <?php include_component('d_action', 'editFollow', array('petition' => $petition)) ?>
      <?php if ($petition->getCampaign()->getDataOwnerId() == $sf_user->getUserId() || $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
      <div>
        <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('petition_delete_', array('id' => $petition->getId())) ?>">Delete Action</a>
      </div>
      <?php endif ?>
  </div>
</div>
