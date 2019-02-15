<?php /* @var $pair MappingPair */
$id = 'pair_form_' . ($pair->isNew() ? 'new' : $pair->getId()); ?>
<tr id="<?php echo $id ?>">
  <td colspan="3">
    <form class="ajax_form form-horizontal" action="<?php echo $pair->isNew() ? url_for('mapping_new_pair', array('id' => $pair->getMappingId())) : url_for('mapping_edit_pair', array('id' => $pair->getId())) ?>" method="post">
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-secondary" href="javascript:(function(){$('#<?php echo $id ?>').remove();})();">Cancel</a>
      </div>
    </form>
  </td>
</tr>
