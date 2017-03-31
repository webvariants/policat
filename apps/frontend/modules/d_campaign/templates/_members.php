<?php
/* @var $campaign Campaign */
/* @var $campaign_rights_list Doctrine_Collection */
use_helper('Date');
?>
<div id="campaign_members" class="well">
  <?php if (isset($campaign_rights_list)): ?>
    <h3>Members</h3>
    <?php if ($admin): ?>
      <form class="ajax_form" method="post" action="<?php echo url_for('campaign_members', array('id' => $campaign->getId())) ?>">
        <p>
          <a class="btn btn-mini submit" data-submit='{"method": "member" }'>Member</a>
          <a class="btn btn-mini submit" data-submit='{"method": "admin" }'>Admin</a>
          <a class="btn btn-mini submit" data-submit='{"method": "block" }'>block</a>
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
          <?php if ($campaign_rights_list->count()) foreach ($campaign_rights_list as $campaign_rights): /* @var $campaign_rights CampaignRights */ ?>
              <tr>
                <td class="single_check"><input type="checkbox" value="<?php echo $campaign_rights->getUserId() ?>" name="ids[]" /></td>
                <td>
                  <?php echo $campaign_rights->getUser()->getFullName() ?>
                </td>
                <td>
                  <?php if (!$campaign_rights->getActive()): ?><span class="label label-info">blocked</span>
                  <?php else: ?>
                    <?php if ($campaign_rights->getAdmin()): ?><span class="label label-important">admin</span>
                    <?php else: ?>
                      <?php if ($campaign_rights->getMember()): ?><span class="label">member</span><?php endif ?>
                    <?php endif ?>
                  <?php endif ?>
                </td>
              </tr>
            <?php endforeach; ?>
        </tbody>
      </table>
      <?php if ($admin): ?>
      </form>
      <div id="members_add">
        <h4>Add members to campaign team</h4>
        <form class="ajax_form form-inline" method="post" action="<?php echo url_for('campaign_members_add', array('id' => $campaign->getId())) ?>">
          <?php echo $form ?>
          <button class="btn btn-primary" type="submit">Add member</button>
          <?php if ($invitations): ?>
          <h4 class="top10">Invitations</h4>
          <?php foreach ($invitations as $invitationCampaign): /* @var $invitationCampaign InvitationCampaign*/ ?>
          <div><?php echo $invitationCampaign->getInvitation()->getEmailAddress() ?> (<?php echo format_date($invitationCampaign->getCreatedAt(), 'yyyy-MM-dd') ?>)</div>
          <?php endforeach; ?>
          <?php endif ?>
        </form>
      </div>
    <?php endif ?>
  <?php endif ?>
</div>