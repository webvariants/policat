<?php if ($form): ?>
  <div id="petition_follow" class="well">
      <h3>Admin functions</h3>
      <form method="post" action="<?php echo url_for('petition_follow', array('id' => $form->getObject()->getId())) ?>" class="ajax_form form-horizontal">
          <?php echo $form ?>
          <button class="btn btn-small" type="submit">Set forwarding</button>
      </form>
      <p>This function forwards the URLs (embed links) of widgets from this action to the selected (follow-up) action. Note: Only widgets that have been copied into the follow-up action will be forwarded. To stop a forwarding, set the forwarding to '-No forwarding-'.</p>
  </div>
<?php endif; ?>