<?php
/* @var $petition Petition */
/* @var $petition_rights_list Doctrine_Collection */
?>
<div id="petition_members" class="card bg-light mb-3">
  <div class="card-body">
  <?php if (isset($petition_rights_list) && $petition_rights_list->count()): ?>
    <h3>Members</h3>
    <?php if ($admin): ?>
      <form class="ajax_form" method="post" action="<?php echo url_for('petition_members', array('id' => $petition->getId())) ?>">
        <p>
          <a class="btn btn-primary btn-sm submit" data-submit='{"method": "member" }'>Editor</a>
          <a class="btn btn-danger btn-sm submit" data-submit='{"method": "block" }'>block</a>
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
          <?php foreach ($petition_rights_list as $petition_rights): /* @var $petition_rights PetitionRights */ ?>
            <tr>
              <?php if ($admin): ?><td class="single_check"><input type="checkbox" value="<?php echo $petition_rights->getUserId() ?>" name="ids[]" /></td><?php endif ?>
              <td>
                <?php echo $petition_rights->getUser()->getFullName() ?>
              </td>
              <td>
                <?php if (!$petition_rights->getActive()): ?><span class="label label-info">blocked</span>
                <?php else: ?>
                  <?php if ($petition_rights->getMember()): ?><span class="label">editor</span><?php endif ?>
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
</div>