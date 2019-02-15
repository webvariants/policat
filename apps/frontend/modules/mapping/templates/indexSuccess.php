<?php include_partial('dashboard/admin_tabs', array('active' => 'mappings')) ?>
<table class="table table-bordered table-striped">
  <thead>
    <tr><th>Name</th><th></th></tr>
  </thead>
  <tbody>
    <?php foreach ($mappings as $mapping): /* @var $mapping Mapping */ ?>
    <tr>
      <td><?php echo $mapping->getName() ?></td>
      <td><a class="btn btn-primary btn-sm" href="<?php echo url_for('mapping_edit', array('id' => $mapping->getId())) ?>">edit</a></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
<a class="btn btn-primary" href="<?php echo url_for('mapping_new') ?>">Create Mapping</a>