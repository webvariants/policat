<div class="page-header">
  <h1>Password request</h1>
</div>
<?php if (isset($user)): ?>
  <?php if ($form): ?>
    <p>Please enter a new password.</p>
    <form id="reset_form" action="" method="post" class="ajax_form form-horizontal">
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Change</button>
      </div>
    </form>
  <?php endif ?>
<?php else: ?>
  <p>Sorry, wrong key or passwort already changed.</p>
<?php endif ?>
