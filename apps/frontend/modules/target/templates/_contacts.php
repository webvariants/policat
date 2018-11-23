<?php if (!isset($no_filter)):
/* @var $target_list MailingList */
?>
  <form method="get" class="form-inline ajax_form filter_form" action="<?php echo url_for('target_contact_pager', array('page' => 1, 'id' => $target_list->getId())) ?>">
  <?php echo $form ?>
    <button class="btn btn-primary top15" type="submit">Filter</button>
    <button class="filter_reset btn btn-small top15">Reset filter</button>
  </form>
<?php endif ?>
<div id="contacts">
  <table class="table table-bordered table-striped">
    <thead>
      <tr><th>E-mail</th><th>Firstname</th><th>Lastname</th><th>Gender</th><th>Country</th><th>Language</th><th>Bounce</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($contacts as $contact): /* @var $contact Contact */ ?>
        <tr id="contact_<?php echo $contact->getId() ?>">
          <td><?php echo $contact->getEmail() ?></td>
          <td><?php echo $contact->getFirstname() ?></td>
          <td><?php echo $contact->getLastname() ?></td>
          <td><?php echo $contact->getGenderName2() ?></td>
          <td><?php echo $contact->getCountry() ?></td>
          <td><?php echo $contact->getLanguage()->getName() ?></td>
          <td>
                <?php if ($contact->getBounce()): ?>
                  <?php echo $contact->getBounceAt() ?>
                  <?php if ($contact->getBounceBlocked()): ?><span class="label label-warning">blocked</span><?php endif ?>
                  <?php if ($contact->getBounceBlocked()): ?><span title="hard bounce" class="label label-important">hard</span><?php endif ?>
                  <br />
                  <?php if ($contact->getBounceError()): ?><code title="bounce error"><?php echo $contact->getBounceError() ?></code><?php else: ?><code title="bounce error">unknown</code><?php endif ?>
                  <?php if ($contact->getBounceRelatedTo()): ?><code title="bounce error related to"><?php echo $contact->getBounceRelatedTo() ?></code><?php endif ?>
                <?php endif ?>
          </td>
          <td>
            <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_contact', array('id' => $contact->getId(), 'page' => $contacts->getPage())) ?>">edit</a>
            <a class="ajax_link btn btn-sm" href="<?php echo url_for('target_contact_delete', array('id' => $contact->getId())) ?>">delete</a>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  <?php include_partial('dashboard/pager', array('pager' => $contacts)) ?>
</div>