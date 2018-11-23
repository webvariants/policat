<?php include_partial('dashboard/admin_tabs', array('active' => 'languages')) ?>
<table class="table table-bordered table-striped">
  <thead>
    <tr><th>ISO Code</th><th>Name</th><th>Order number</th><th></th></tr>
  </thead>
  <tbody>
    <?php foreach ($languages as $language): /* @var $language Language */ ?>
    <tr>
      <td><?php echo $language->getId() ?></td>
      <td><?php echo $language->getName() ?></td>
      <td><?php echo $language->getOrderNumber() ?></td>
      <td><a class="btn btn-sm" href="<?php echo url_for('language_edit', array('id' => $language->getId())) ?>">edit</a></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
<a class="btn" href="<?php echo url_for('language_new') ?>">Create Language</a>