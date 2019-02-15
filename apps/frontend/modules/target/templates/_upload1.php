<form id="upload_form" class="ajax_form form-horizontal" action="<?php echo url_for('target_upload', array('id' => $target_list->getId())) ?>" method="post" enctype="multipart/form-data">
  <h2>Upload contacts</h2>
  <p>First line in CSV-File must contain table headers. The CSV-File MUST be UTF-8 encoded.</p>
  <?php echo $form ?>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Upload</button>
    <a class="btn btn-secondary" href="javascript:(function(){$('#upload_form').remove();})();">Cancel</a>
  </div>
</form>
