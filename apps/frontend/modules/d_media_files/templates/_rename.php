<div class="modal hide hidden_remove" id="media_file_rename_modal">
    <form method="post" class="form-inline ajax_form" action="<?php echo url_for('media_files_rename', array('id' => $form->getObject()->getId())) ?>">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
            <h3>Rename media file</h3>
        </div>
        <div class="modal-body">
            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form ?>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <a class="btn btn-default" data-dismiss="modal">Cancel</a>
        </div>
    </form>
</div>