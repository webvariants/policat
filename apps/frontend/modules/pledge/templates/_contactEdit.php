<?php
/* @var $form PetitionContactForm */
$petition_contact = $form->getObject();
$petition = $petition_contact->getPetition();
$contact = $petition_contact->getContact();
?>
<tr id="contact_edit_row_<?php echo $contact->getId() ?>">
  <td colspan="7">
    <form class="ajax_form form-horizontal" action="<?php echo url_for('pledge_contact_edit', array('petition_id' => $petition->getId(), 'id' => $contact->getId())) ?>" method="post">
      <?php echo $form ?>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn" onclick="javascript:$('#contact_edit_row_<?php echo $contact->getId() ?>').remove();return false;">Cancel</button>
      </div>
    </form>
  </td>
</tr>