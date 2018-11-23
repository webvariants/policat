<?php
/* @var $target_list target_list */
/* @var $target_list_rights_list Doctrine_Collection */
?>
<div id="target_list_members">
  <?php if (isset($target_list_rights_list) && $target_list_rights_list->count()): ?>
    <h2>Members</h2>
    <?php if ($admin): ?>
      <form class="ajax_form" method="post" action="<?php echo url_for('target_members', array('id' => $target_list->getId())) ?>">
        <p>
          <a class="btn btn-sm submit" data-submit='{"method": "enable" }'>Editor</a>
          <a class="btn btn-sm submit" data-submit='{"method": "disable" }'>Disable</a>
        </p>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>" />
      <?php endif ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <?php if ($admin): ?><th></th><?php endif ?>
            <th>Name</th>
            <th>Rights</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($target_list_rights_list as $target_list_rights): /* @var $target_list_rights target_listRights */ ?>
            <tr>
              <?php if ($admin): ?><td class="single_check"><input type="checkbox" value="<?php echo $target_list_rights->getUserId() ?>" name="ids[]" /></td><?php endif ?>
              <td>
                <?php echo $target_list_rights->getUser()->getFullName() ?>
              </td>
              <td>
                <?php if ($target_list_rights->getActive()): ?>
                  <span class="label label-info">editor</span>
                <?php else: ?>
                  <span class="label">disabled</span>
                <?php endif ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if ($admin): ?>
      </form>
    <?php endif ?>
  <?php endif ?>
</div>