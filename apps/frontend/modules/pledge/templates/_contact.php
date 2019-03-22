<?php if ($pledges instanceof sfOutputEscaperArrayDecorator)
  $pledges = $pledges->getRawValue();
?>
<tr id="contact_<?php echo $contact->getId() ?>">
  <td><?php echo $contact->getEmail() ?></td>
  <td><?php echo $contact->getFirstname() ?></td>
  <td><?php echo $contact->getLastname() ?></td>
  <td><?php echo $contact->getGenderName2() ?></td>
  <td><?php echo $contact->getCountry() ?></td>
  <td>
    <?php
    foreach ($active_pledge_item_ids as $pledge_item_id):
      $pledge_item = $pledge_items[$pledge_item_id]; /* @var $pledge_item PledgeItem */
      $status = null;
      if (array_key_exists($contact->getId(), $pledges) && array_key_exists($pledge_item_id, $pledges[$contact->getId()])):
        $status = $pledges[$contact->getId()][$pledge_item_id];
      endif;
      switch ($status):
        case PledgeTable::STATUS_YES:
          ?>
          <span class="pledge_select add_tooltip" title="<?php echo $pledge_item->getName() ?>"><i class="pledge_color pledge_yes pledge_inline pledge_color_<?php echo $pledge_item->getColor() ?>"></i></span>
          <?php
          break;
        case PledgeTable::STATUS_NO:
          ?>
          <span class="pledge_select add_tooltip" title="<?php echo $pledge_item->getName() ?>"><i class="pledge_color pledge_no pledge_inline pledge_color_<?php echo $pledge_item->getColor() ?>"></i></span>
          <?php
          break;
        default:
          ?>
          <span class="pledge_select add_tooltip" title="<?php echo $pledge_item->getName() ?>"><i class="pledge_color pledge_no_comment pledge_inline pledge_color_<?php echo $pledge_item->getColor() ?>"></i></span>
        <?php
      endswitch;
    endforeach;
    $petition_contact = $contact->getPetitionContactByPetition($petition_id);
    if ($petition_contact && trim($petition_contact->getComment())):
      ?>
      <span class="label add_tooltip" title="<?php echo $petition_contact->getComment() ?>">comment</span>
      <?php
    endif
    ?>
  </td>
  <td>
    <a class="ajax_link btn-primary btn btn-sm" href="<?php echo url_for('pledge_contact_edit', array('petition_id' => $petition_id, 'id' => $contact->getId())) ?>">edit</a>
  </td>
</tr>