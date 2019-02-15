<?php
$contact = $form->getObject();
?>
<tr id="contact_form_<?php echo $contact->getId() ?>">
  <td colspan="6">
    <form class="ajax_form form-horizontal" action="<?php echo url_for($route, $route_params->getRawValue()) ?>" method="post">
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-secondary" href="javascript:(function(){$('#contact_form_<?php echo $contact->getId() ?>').remove();})();">Cancel</a>
      </div>
    </form>
  </td>
</tr>
