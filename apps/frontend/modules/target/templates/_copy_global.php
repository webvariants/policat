<div class="modal hide hidden_remove" id="target_copy_global_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="ajax_form" action="<?php echo url_for('target_copy_global', array('id' => $id)) ?>"
                method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Copy Target-list from global pool</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $form ?>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-secondary" data-dismiss="modal">Close</a>
                    <button class="btn btn-primary" type="submit">Copy</button>
                </div>
            </form>
        </div>
    </div>
</div>