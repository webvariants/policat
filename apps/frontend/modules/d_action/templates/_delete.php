<div class="modal hide hidden_remove" id="petition_delete_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="ajax_form" action="<?php echo url_for('petition_delete_', array('id' => $id)) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Warning: This will delete the action "<b><?php echo $name ?></b>" including texts, widgets, counters and all activist data.
                        This will include any data owned by widget-owners. Make sure to export/download your data first.
                        Do you want to proceed and delete this action?
                    </p>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>"/>
                    <div class="control-group">
                        <label for="delete_action_name" class="control-label">
                            Enter the action title to confirm.
                        </label>
                        <div class="controls">
                            <input id="delete_action_name" class="form-control" type="text" name="name" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete action</button>
                    <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
