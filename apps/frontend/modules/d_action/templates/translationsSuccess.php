<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
    <li  class="breadcrumb-item active">Translations</li>
  </ol>
</nav>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('tabs', array('petition' => $petition, 'active' => 'translations')) ?>
<?php if ($can_not_create_widget_from_draft) include_partial('dashboard/alert', array('heading' => 'Info', 'message' => 'You can not create a widget of a draft translation.')) ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Language</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($translations as $translation): /* @var $translation PetitionText */ ?>
          <tr>
            <td><?php echo $translation->getLanguage() ?></td>
            <td><?php echo $translation->getStatusName() ?></td>
            <td>
              <a class="btn btn-secondary btn-sm" href="<?php echo url_for('translation_edit', array('id' => $translation->getId())) ?>">edit</a>
              <?php if ($translation->getStatus() == PetitionText::STATUS_ACTIVE): ?>
                <a class="btn btn-secondary btn-sm post ajax_link" data-submit='<?php echo json_encode(array('lang' => $translation->getId())) ?>' href="<?php echo url_for('widget_create', array('id' => $petition->getId())) ?>">create widget</a>
              <?php endif ?>
              <?php if ($petition->getKind() == Petition::KIND_PLEDGE): ?>
                <a class="btn btn-secondary btn-sm" href="<?php echo url_for('translation_edit', array('id' => $translation->getId())) ?>#pledges">edit pledges</a>
                <a target="_blank" class="btn btn-secondary btn-sm" href="<?php echo url_for('pledge_contact_test', array('petition_id' => $petition->getId())) ?>?lang=<?php echo $translation->getLanguageId() ?>">Test pledge page</a>
              <?php endif ?>
              <a class="btn btn-secondary btn-sm" href="<?php echo url_for('translation_create', array('id' => $petition->getId())) ?>?copy=<?php echo $translation->getId() ?>">Copy for new translation</a>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <a class="btn btn-secondary btn-sm" href="<?php echo url_for('translation_create', array('id' => $petition->getId())) ?>">Create new translation</a>
