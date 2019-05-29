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
      <strong>Status:</strong> <?php echo $petition->getStatusName() ?><br />
      <strong>Input data</strong>
      <ul>
        <li><?php echo Petition::$NAMETYPE_SHOW[$petition->getNametype()] ?></li><?php
        if ($petition->getWithAddress()) echo '<li>' . Petition::$WITH_ADDRESS_SHOW[$petition->getWithAddress()] . '</li>';
        if ($petition->getWithExtra1() == Petition::WITH_EXTRA_YES) echo '<li>free text field</li>';
        if ($petition->getWithCountry()): ?><li>country</li><?php else: ?><li>without country</li><?php endif;
        if ($petition->getWithComments()): ?><li>comments</li><?php endif ?>
      </ul>
      <?php if ($petition->getFollowPetitionId()): ?>
        <h4>Follow-up action</h4>
        <p><?php echo $petition->getFollowPetition()->getName() ?></p>
      <?php endif ?>
      <table class="table table-responsive table-sm">
        <?php if ($petition->isGeoKind()): ?>
        <tr>
          <td class="text-right table-success"><?php echo format_number($petition->countMailsSent()) ?></td>
          <th>Mails sent</th>
        </tr>
        <tr>
          <td class="text-right"><?php echo format_number($petition->countMailsOutgoing()) ?></td>
          <th>Mails in Sending Queue</th>
        </tr>
        <tr>
          <td class="text-right"><?php echo format_number($petition->countMailsPending()) ?></span></div>
          <th>Mails pending</th>
        </div>
        <?php endif ?>
        <tr>
          <td class="text-right"><?php echo format_number($petition->countSignings(60)) ?></td>
          <th>Signings via widgets</th>
        </tr>
        <tr>
          <td class="text-right"><?php echo format_number($petition->sumApi(60)) ?></td>
          <th>Signings via API</th>
        </tr>
        <tr>
          <td class="text-right"><?php echo format_number($petition->getAddnum()) ?></td>
          <th>Manual counter tweak</th>
        </tr>
        <tr>
          <td class="text-right table-success"><?php echo format_number($petition->countSigningsPlusApi(60)) ?></td>
          <th>Signings total</th>
        </tr>
        <tr>
          <td class="text-right"><?php echo format_number($petition->countSignings24()) ?></td>
          <th>Signings last 24h</th>
        </tr>
        <tr>
          <td class="text-right"><?php echo format_number($petition->countSigningsPending()) ?></td>
          <th>Signings with verification pending</th>
        </tr>
        <tr>
          <td class="text-right"><?php echo format_number($petition->countWidgets()) ?></td>
          <th>Widgets</th>
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
