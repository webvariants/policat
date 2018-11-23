<?php
/* @var $ticket Ticket */
use_helper('Date');
?>
<div class="well">
    <?php echo $sf_data->getRaw('text'); ?>
    <?php if (isset($csrf_token)): ?>
      <form class="ajax_form bottom0" method="post" action="<?php echo url_for('ticket_action') ?>">
          <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>" />
          <input type="hidden" value="<?php echo $ticket->getId() ?>" name="id" />
          <?php if (isset($campaign_id)): ?><input type="hidden" name="campaign_id" value="<?php echo $campaign_id ?>" /><?php endif ?>
          <?php if (isset($petition_id)): ?><input type="hidden" name="petition_id" value="<?php echo $petition_id ?>" /><?php endif ?>
          <div class="align-right">
              <?php if ($is_notice): ?>
                <a class="btn btn-primary btn-sm submit" data-submit='{"method": "decline", "view": "close" }'>Close</a>
              <?php else: ?>
                <a class="btn btn-success btn-sm submit" data-submit='{"method": "approve", "view": "close" }'>Approve</a>
                <a class="btn btn-danger btn-sm submit" data-submit='{"method": "decline", "view": "close" }'>Decline</a>
              <?php endif ?>
          </div>
      </form>
  </form>
<?php endif
?>
</div>