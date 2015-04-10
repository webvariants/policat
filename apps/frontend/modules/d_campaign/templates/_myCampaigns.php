<?php
/* @var $create_form NewCampaignNameForm */
/* @var $join_form SelectCampaignForm */
/* @var $leave_form SelectCampaignForm */
?>
<?php if (isset($join_form) || isset($edit_form)): ?>
  <div id="my_campaigns" class="span4">
      <div class="well">
          <h3>My campaigns</h3>
          <?php if (isset($edit_form)): ?>
            <form id="campaign_admin_go_edit" class="ajax_form form-inline" action="<?php echo url_for('campaign_go_edit') ?>" method="post">
                <?php echo $edit_form ?>
                <button class="btn btn-small button-medium">edit</button>
            </form>
          <?php endif ?>
          <?php if (isset($join_form)): ?>
            <?php if (isset($create_form)): ?>
              <form id="my_campaigns_create" class="ajax_form form-inline" action="<?php echo url_for('campaign_create_') ?>" method="post">
                  <?php echo $create_form ?>
                  <button class="btn btn-small button-medium">create</button>
              </form>
            <?php endif ?>
            <form id="my_campaigns_join" class="ajax_form form-inline" action="<?php echo url_for('campaign_join') ?>" method="post">
                <?php echo $join_form ?>
                <button class="btn btn-small button-medium">join</button>
            </form>
          <?php endif ?>
      </div>
  </div>
  <?php
 endif ?>