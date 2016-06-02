<form id="widget_edit_form" class="form-horizontal" action="<?php echo url_for('media_files_rename', array('petition_id' => $petition->getId(), "id"=>$form->getObject()->getId())) ?>" method="post">  
  <?php echo $form->renderHiddenFields() ?>  
  <div class="row">
    <div class="span6"><?php echo $form['name']?></div>   
    <div class="span6"><?php echo $form['name']->renderError() ?></div>   
  </div> 
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Rename</button>    
    <a class="btn" href="<?php echo url_for('media_files_list', array('id' => $petition->getId())) ?>">Cancel</a>
  </div>
</form>