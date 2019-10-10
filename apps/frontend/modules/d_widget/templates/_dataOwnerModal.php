<?php
    $campaign = $widget->getCampaign();
    $officer = $campaign->getDataOwnerId() ? $campaign->getDataOwner() : null; /* @var $officer sfGuardUser */
?>
<div class="modal hide hidden_remove" id="data_owner_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form class="ajax_form" action="<?php echo url_for('widget_data_owner') ?>" method="post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token, 'id' => $widget->getId(), 'agree' => 1)) ?>'>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">You must accept the widget data owner agreement to request data ownership.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <p class="text-monospace" style="white-space: pre-wrap"><?php echo $widget->getCampaign()->getPrivacyPolicy() ?></p>
                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="agree-check" id="agree-check">
                        <label class="form-check-label" for="agree-check">
                            I have read and accepted the data owner agreement with DPO "<?php echo $officer ? $officer->getFullName() : 'nobody' ?>". I will handle all personal activist data in accordance with the privacy policy of this action.
                        </label>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Become data owner</button>
                    <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>