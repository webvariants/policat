<?php /* @var $pledge_item PledgeItem */
?>
<tr id="pledge_item_<?php echo $pledge_item->getId() ?>">
  <td><?php echo $pledge_item->getName() ?></td>
  <td><?php echo $pledge_item->getStatusName() ?></td>
  <td>
    <strong><?php echo $pledge_item->countPledgeStatus(PledgeTable::STATUS_YES) ?></strong>&nbsp;&times;&nbsp;<span class="pledge_select"><i class="pledge_color pledge_color_<?php echo $pledge_item->getColor() ?>"></i></span>
    <strong><?php echo $pledge_item->countPledgeStatus(PledgeTable::STATUS_NO) ?></strong>&nbsp;&times;&nbsp;<span class="pledge_select"><i class="pledge_color pledge_no pledge_color_<?php echo $pledge_item->getColor() ?>"></i></span>
    <strong><?php echo $pledge_item->countPledgeStatus(PledgeTable::STATUS_NO_COMMENT) ?></strong>&nbsp;&times;&nbsp;<span class="pledge_select"><i class="pledge_color pledge_no_comment pledge_color_<?php echo $pledge_item->getColor() ?>"></i></span>
  </td>
  <td>
    <a class="ajax_link btn-primary btn btn-sm" href="<?php echo url_for('pledge_edit', array('id' => $pledge_item->getId())) ?>">edit</a>
  </td>
</tr>