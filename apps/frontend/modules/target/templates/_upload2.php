<form id="upload_form" class="ajax_form form-horizontal" action="<?php echo url_for('target_upload', array('id' => $target_list->getId())) ?>" method="post">
  <h2>Upload contacts</h2>
  <p>Please select the column of each information.</p>
  <?php echo $form ?>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Upload</button>
    <a class="btn" href="javascript:(function(){$('#upload_form').remove();})();">Cancel</a>
  </div>
</form>
