<?php
/* @var $petition Petition */
$user = $sf_user->getGuardUser()->getRawValue();
/* @var $user sfGuardUser */
if ($petition->isEditableBy($user)) {
  $list = array(
      'overview' => array('title' => 'Overview', 'route' => 'petition_overview'),
      'edit' => array('title' => 'Settings', 'route' => 'petition_edit_'),
      'targets' => null,
      'pledges' => null,
      'translations' => array('title' => 'Translations', 'route' => 'petition_translations'),
      'widgets' => array('title' => 'Widgets', 'route' => 'petition_widgets'),
      'files' => array('title' => 'Media Files', 'route' => 'media_files_list')
  );
} else {
  $list = array();
}
if ($petition->isCampaignAdmin($user)) {
  $list['todo'] = array('title' => 'To-Do', 'route' => 'petition_todo');
  $list['tokens'] = array('title' => 'Counter &amp; API', 'route' => 'petition_tokens');
}
if ($user->isPetitionMember($petition->getRawValue(), true)) {
  $list['data'] = array('title' => 'Participants', 'route' => 'petition_data');
  if ($user->isDataOwnerOfCampaign($petition->getRawValue()->getCampaign())) {
    $list['dataSubscriptions'] = array('title' => 'Mailing addresses', 'route' => 'petition_data_email');
    $list['bounces'] = array('title' => 'Bounces', 'route' => 'petition_bounces');
  }
}
if ($petition->getKind() == Petition::KIND_PLEDGE) {
  $list['pledges'] = array('title' => 'Pledges', 'route' => 'pledge_list');
  $list['pledge_stats'] = array('title' => 'Results', 'route' => 'pledge_stats');
}
if ($petition->isGeoKind()) {
  $list['targets'] = array('title' => 'Targets', 'route' => 'target_petition_edit');
}

if (!isset($active))
  $active = '';
?>
<div class="page-header">
  <h1><?php echo $petition->getKindName() ?>: <?php echo $petition->getName() ?></h1>
</div>
<?php if ($list): ?>
  <ul class="nav nav-tabs">
    <?php foreach ($list as $key => $entry): if ($entry): ?>
        <li class="<?php if ($active == $key) echo 'active' ?>">
          <a href="<?php echo url_for($entry['route'], array('id' => $petition->getId())) ?>"><?php echo $entry['title'] ?></a>
        </li>
      <?php endif;
    endforeach ?>
  </ul>
  <?php
 endif ?>