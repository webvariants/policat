<h1>Menu</h1>
<ul>
  <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_SYSTEM)): ?>
  <li><a href="<?php echo url_for('sf_guard_group') ?>">sfGuardGroup</a></li>
  <?php endif ?>
  <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_SYSTEM)): ?>
  <li><a href="<?php echo url_for('sf_guard_user') ?>">sfGuardUser</a></li>
  <?php endif ?>
  <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_SYSTEM)): ?>
  <li><a href="<?php echo url_for('sf_guard_permission') ?>">sfGuardPermission</a></li>
  <?php endif ?>
  <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_SYSTEM)): ?>
  <li><a href="<?php echo url_for('campaign') ?>">Campaign</a></li>
  <li><a href="<?php echo url_for('petition') ?>">Petition</a></li>
  <li><a href="<?php echo url_for('petition_text') ?>">Petition text</a></li>
  <?php endif ?>
</ul>