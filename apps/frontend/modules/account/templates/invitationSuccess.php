<div class="page-header">
    <h1>Transfer invitation</h1>
</div>

<?php if ($invitation): /* @var $invitation Invitation */ ?>
  <form method="POST" action="<?php echo url_for('invitation') ?>">
      <input type="hidden" name="code" value="<?php echo $invitation->getId() ?>-<?php echo $invitation->getValidationCode() ?>" />
      <?php echo $form->renderHiddenFields() ?>

      <h4>You are invited to campaign:</h4>
      <?php foreach ($invitation->getInvitationCampaign() as $invitationCampaign): /* @var $invitationCampaign InvitationCampaign */ ?>
        <div><?php echo $invitationCampaign->getCampaign()->getName() ?></div>
      <?php endforeach ?>
      <button type="submit" class="btn btn-primary top30">Transfer invitation to account <?php echo $sf_user->getName() ?></button>
  </form>
<?php else: ?>
  <p>No valid invitation.</p>
<?php endif; ?>
