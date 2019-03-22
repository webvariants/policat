<div class="page-header">
    <h1>Create new account</h1>
</div>
<div id="register_form">
    <form class="ajax_form form-horizontal" action="<?php echo url_for('register') ?>" method="post" autocomplete="off">
        <div class="row">
            <div class="col-6">
                <?php echo $form->renderHiddenFields() ?>
                <?php echo $form->renderRows('email_address', 'password', 'password_again') ?>
            </div>
            <div class="col-6">
                <?php echo $form->renderRows('first_name', 'last_name', 'organisation') ?>
            </div>
            <div class="col-12">
                <?php echo $form['terms']->renderRow() ?>
                <?php if ($invitation): /* @var $invitation Invitation */ ?>
                  <input type="hidden" name="invitation" value="<?php echo $invitation->getId() ?>-<?php echo $invitation->getValidationCode() ?>" />
                  <fieldset>
                      <div class="control-group">
                          <div class="controls">
                              <h4>You are invited to campaign:</h4>
                              <?php foreach ($invitation->getInvitationCampaign() as $invitationCampaign): /* @var $invitationCampaign InvitationCampaign */ ?>
                                <div><?php echo $invitationCampaign->getCampaign()->getName() ?></div>
                              <?php endforeach ?>
                          </div>
                      </div>
                  </fieldset>
                <?php endif ?>
                <fieldset><div class="control-group"><div class="controls"><?php include_partial('account/captcha', array('onLoad' => true)) ?></div></div></fieldset>
            </div>
            <fieldset><div class="control-group"><div class="controls"><div class="register-success"></div></div></div></fieldset>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary disable-on-captcha" type="submit">Register</button>
            <a class="btn btn-secondary" href="<?php echo url_for('homepage') ?>">Cancel</a>
        </div>
    </form>
</div>
