<?php 
include_partial('dashboard/admin_tabs', array('active' => 'undelete'));
?>
<h2>Deleted Campaigns</h2>
<?php if ($campaigns->count()): ?>
<table class="table table-striped table-bordered">
  <tbody>
    <?php foreach ($campaigns as $campaign): /* @var $campaign Campaign */ ?>
    <tr>
      <td><?php echo $campaign->getName() ?></td>
      <td><a class="btn btn-mini" href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>">edit</a></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
<?php else: ?>
<p>None.</p>
<?php endif; ?>
