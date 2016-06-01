<ul class="breadcrumb">
  <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
  <li class="active">Media Files</li>
</ul>
<div class="page-header">
  <h1>Media Files</h1>
</div>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('d_action/tabs', array('petition' => $petition, 'active' =>  'files')) ?>
<?php include_component('d_media_files', 'list',array("petition"=>$petition->getId(),  "form"=>$form)) ?>