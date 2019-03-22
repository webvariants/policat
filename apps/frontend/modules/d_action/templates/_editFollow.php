<?php if (isset($form)): ?>
  <div id="petition_follow" class="card bg-light mb-3">
    <div class="card-body">
      <h3>Admin functions</h3>
      <form method="post" action="<?php echo url_for('petition_follow', array('id' => $form->getObject()->getId())) ?>" class="ajax_form form-horizontal">
          <?php echo $form ?>
          <br />
          <button class="btn btn-primary btn-small" type="submit">Set forwarding</button>
          <br />
          <br />
      </form>
      <p>This function forwards the URLs (embed links) of widgets from this action to the selected (follow-up) action. Note: Only widgets that have been copied into the follow-up action will be forwarded. To stop a forwarding, set the forwarding to '-No forwarding-'.</p>
    </div>
  </div>
<?php endif; ?>