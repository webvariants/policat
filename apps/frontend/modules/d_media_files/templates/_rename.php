<div class="modal hide hidden_remove" id="media_file_rename_modal" tabindex="-1" role="dialog">
    <form method="post" class="form ajax_form" action="<?php echo url_for('media_files_rename', array('id' => $form->getObject()->getId())) ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename media file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $form->renderHiddenFields() ?>
                    <?php echo $form ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>