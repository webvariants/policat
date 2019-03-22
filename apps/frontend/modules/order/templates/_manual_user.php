<div class="modal hide hidden_remove modal-large" id="order_manual_user">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>Select a user for the order</h3>
    </div>
    <div class="modal-body">
        <?php if ($users->count()): ?>
          <?php foreach ($users as $user): /* @var $user sfGuardUser */ ?>
            <a href="<?php echo url_for('order_manual', array('id' => $quota->getId(), 'user_id' => $user->getId())) ?>"><?php echo $user->getFullName() ?></a><br />
          <?php endforeach ?>
        <?php else: ?>
            No users in this campaign.
        <?php endif ?>
    </div>
    <div class="modal-footer">
        <a class="btn btn-secondary" data-dismiss="modal">Close</a>
    </div>
</div>