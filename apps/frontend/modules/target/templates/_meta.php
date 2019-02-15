<?php
$meta = $form->getObject();
?>
<tr id="meta_form_<?php echo $meta->getId() ?>">
  <td colspan="4">
    <form class="ajax_form form-horizontal" action="<?php echo url_for($route, $route_params->getRawValue()) ?>" method="post">
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-secondary" href="javascript:(function(){$('#meta_form_<?php echo $meta->getId() ?>').remove();})();">Cancel</a>
      </div>
    </form>
  </td>
</tr>
