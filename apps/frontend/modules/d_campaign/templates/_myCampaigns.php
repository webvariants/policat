<?php
/* @var $join_form SelectCampaignForm */
?>
<div id="my_campaigns" class="span4">
    <div class="well">
        <?php if (isset($list)): ?>
          <?php if ($list && $list->count()): ?>
            <h3>My campaigns</h3><br />
            <?php foreach ($list as $campaign): /* @var $campaign Campaign */ ?>
              <a href="<?php echo url_for('campaign_edit_', array('id' => $campaign->getId())) ?>"><?php echo $campaign->getName() ?></a><br />
            <?php endforeach ?>
            <br />
          <?php
          endif;
        endif
        ?>
        <a class="btn btn-primary btn-large" href="<?php echo url_for('petition_new_') ?>">Start new e-action</a>
    </div>
</div>