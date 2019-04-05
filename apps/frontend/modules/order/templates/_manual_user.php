<div class="modal hide hidden_remove" id="order_manual_user" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select a user for the order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <?php if ($users->count()): ?>
                <?php foreach ($users as $user): /* @var $user sfGuardUser */ ?>
                <a
                    href="<?php echo url_for('order_manual', array('id' => $quota->getId(), 'user_id' => $user->getId())) ?>"><?php echo $user->getFullName() ?></a><br />
                <?php endforeach ?>
                <?php else: ?>
                No users in this campaign.
                <?php endif ?>
            </div>
            <div class="modal-footer">
                <a class="btn btn-secondary" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>