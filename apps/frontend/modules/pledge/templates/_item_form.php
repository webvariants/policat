<tr id="pledge_item_form_<?php echo $pledge_item->getId() ?>">
  <td colspan="6">
    <form class="ajax_form form-horizontal" action="<?php echo url_for($route, $route_params->getRawValue()) ?>" method="post">
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-secondary" href="javascript:(function(){$('#pledge_item_form_<?php echo $pledge_item->getId() ?>').remove();})();">Cancel</a>
      </div>
    </form>
  </td>
</tr>
