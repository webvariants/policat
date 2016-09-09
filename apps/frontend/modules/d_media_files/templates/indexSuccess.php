<ul class="breadcrumb">
    <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><span class="divider">/</span>
    <li class="active">Media Files</li>
</ul>
<div class="page-header">
    <h1>Media Files</h1>
</div>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'files')) ?>
<?php include_component('d_media_files', 'list', array('petition' => $petition, 'form' => $form)) ?>
<div>
    <form method="post" class="form-inline ajax_form" action="<?php echo url_for('media_files_upload', array('id' => $petition->getId())) ?>" enctype="multipart/form-data">
        <?php echo $form->renderHiddenFields() ?>
        <span>Upload new media file</span>
        <?php echo $form ?>
        <button class="btn btn-small" type="submit">Upload</button>
    </form>
</div>