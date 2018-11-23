<?php include_partial('dashboard/admin_tabs', array('active' => 'country')) ?>
<table class="table table-bordered table-striped">
  <thead>
      <tr><th class="span3">Name</th><th>Countries</th><th class="span2"></th></tr>
  </thead>
  <tbody>
    <?php foreach ($list as $collection): /* @var $collection CountryCollection */ ?>
    <tr>
      <td><?php echo $collection->getName() ?></td>
      <td><?php echo $collection->getCountries() ?></td>
      <td><a class="btn btn-sm" href="<?php echo url_for('country_edit', array('id' => $collection->getId())) ?>">edit</a></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
<a class="btn" href="<?php echo url_for('country_new') ?>">Create new country collection</a>