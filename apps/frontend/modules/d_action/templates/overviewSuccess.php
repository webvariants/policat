<?php use_helper('Number') ?>
<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li><span class="divider">/</span>
  <li><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li><span class="divider">/</span>
  <li class="active">Overview</li>
</ul>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'overview')) ?>
<div class="row">
  <div class="span8">
    <div class="form-horizontal">
      <fieldset>
        <div class="control-group">
          <label class="control-label">Input data</label>
          <div class="controls">
            <span class="widget_text">
              <?php echo Petition::$NAMETYPE_SHOW[$petition->getNametype()] ?>, <?php
              if ($petition->getWithAddress()) echo Petition::$WITH_ADDRESS_SHOW[$petition->getWithAddress()] . ', ';
              if ($petition->getWithExtra1() == Petition::WITH_EXTRA_YES) echo 'free text field, ';
              if ($petition->getWithCountry()): ?>country<?php else: ?> without country<?php endif;
              if ($petition->getWithComments()): ?>, comments<?php endif ?>
            </span>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Status</label>
          <div class="controls"><span class="widget_text">
            <?php echo $petition->getStatusName() ?></span>
          </div>
        </div>
        <?php if ($petition->getFollowPetitionId()): ?>
        <div class="control-group">
          <label class="control-label">Follow-up action</label>
          <div class="controls"><span class="widget_text"><?php echo $petition->getFollowPetition()->getName() ?></span></div>
        </div>
        <?php endif ?>
        <br />
        <?php if ($petition->isGeoKind()): ?>
        <div class="control-group">
          <label class="control-label">Mails sent</label>
          <div class="controls"><span class="widget_text"><span class="label label-success"><?php echo format_number($petition->countMailsSent()) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Mails in Sending Queue</label>
          <div class="controls"><span class="widget_text"><span class="label label-warning"><?php echo format_number($petition->countMailsOutgoing()) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Mails pending</label>
          <div class="controls"><span class="widget_text"><span class="label label-important"><?php echo format_number($petition->countMailsPending()) ?></span></span></div>
        </div>
        <?php endif ?>
        <div class="control-group">
          <label class="control-label">Signings via widgets</label>
          <div class="controls"><span class="widget_text"><span class="label"><?php echo format_number($petition->countSignings(60)) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Signings via API</label>
          <div class="controls"><span class="widget_text"><span class="label"><?php echo format_number($petition->sumApi(60)) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Manual counter tweak</label>
          <div class="controls"><span class="widget_text"><span class="label"><?php echo format_number($petition->getAddnum()) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Signings total</label>
          <div class="controls"><span class="widget_text"><span class="label label-success"><?php echo format_number($petition->countSigningsPlusApi(60)) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Signings last 24h</label>
          <div class="controls"><span class="widget_text"><span class="label"><?php echo format_number($petition->countSignings24()) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Signings with verification pending</label>
          <div class="controls"><span class="widget_text"><span class="label label-important"><?php echo format_number($petition->countSigningsPending()) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label">Widgets</label>
          <div class="controls"><span class="widget_text"><span class="label label-info"><?php echo format_number($petition->countWidgets()) ?></span></span></div>
        </div>
        <div class="control-group">
          <label class="control-label"></label>
          <div class="controls"><span class="widget_text"><a href="<?php echo url_for('api_v2_doc') ?>" target="_blank">API documentation</a></span></div>
        </div>
      </fieldset>
    </div>
  </div>
  <div class="span4">
      <?php if ($petition->getStatus() == Petition::STATUS_DELETED && $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
          <div class="well">
          <a class="btn btn-danger btn-mini ajax_link" href="<?php echo url_for('petition_delete_', array('id' => $petition->getId())) ?>">Wipe Action</a>
          </div>
        <?php endif ?>
      <?php include_component('d_action', 'members', array('petition' => $petition, 'no_admin' => false)) ?>
      <?php include_component('d_action', 'editFollow', array('petition' => $petition)) ?>
  </div>
</div>