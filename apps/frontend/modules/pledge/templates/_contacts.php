<?php
/* @var $petition_id int */
/* @var $active_pledge_item_ids array */
/* @var $pledges array */
/* @var $pledge_items array */
if ($pledges instanceof sfOutputEscaperArrayDecorator)
  $pledges = $pledges->getRawValue();
?>
<div id="contacts">
  <table class="table table-bordered table-striped">
    <thead>
      <tr><th>E-mail</th><th>Firstname</th><th>Lastname</th><th>Gender</th><th>Country</th><th>Pledges</th><th></th></tr>
    </thead>
    <tbody>
      <?php
      foreach ($contacts as $contact) { /* @var $contact Contact */
        include_partial('contact', array(
            'contact' => $contact,
            'active_pledge_item_ids' => $active_pledge_item_ids,
            'pledges' => $pledges,
            'pledge_items' => $pledge_items,
            'petition_id' => $petition_id
        ));
      }
      ?>
    </tbody>
  </table>
  <?php include_partial('dashboard/pager', array('pager' => $contacts)) ?>
</div>
<a class="btn btn-secondary btn-sm" href="<?php echo url_for('pledge_download', array('id' => $petition_id)) ?>">Download</a>