<div class="modal hide hidden_remove" id="signing_bounce_delete_modal">
    <form class="ajax_form" method="post" action="<?php echo url_for('petition_bounce_delete', array('id' => $petition->getId())) ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
        <input type="hidden" name="sure" value="yes">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
            <h3>Alert</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure to <strong>irrevocably delete</strong> the following data records?</p>
            <ul>
                <?php foreach ($signings as $signing): /* @var $signing PetitionSigning */ ?>
                  <li>
                      <?php echo $signing->getEmailScramble() ?>
                      <input type="hidden" name="ids[]" value="<?php echo $signing->getId() ?>">
                  </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="modal-footer">
            <button class="btn btn-danger">Yes. Delete data records</button>
            <a class="btn btn-primary" data-dismiss="modal">Cancel</a>
        </div>
    </form>
</div>