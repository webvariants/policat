<?php /* @var $ticket Ticket */ ?>
<div class="modal hide hidden_remove" id="ticket_view_modal">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">&times;</a>
      <h3>Ticket</h3>
    </div>
    <div class="modal-body">
      <table class="table table-condensed">
        <tbody>
          <h4><?php echo $ticket->getKindName() ?></h4><br />
          <?php if ($ticket->getFromId()): ?><tr><th>From</th><td><?php echo $ticket->getFrom()->getFullName() ?></td></tr><?php endif ?>
          <?php if ($ticket->getToId()): ?><tr><th>To</th><td><?php echo $ticket->getTo()->getFullName() ?></td></tr><?php endif ?>
          <?php if ($ticket->getCampaignId()): ?><tr><th>Campaign</th><td><?php echo $ticket->getCampaign()->getName() ?></td></tr><?php endif ?>
          <?php if ($ticket->getPetitionId()): ?><tr><th>Action</th><td><?php echo $ticket->getPetition()->getName() ?></td></tr><?php endif ?>
          <?php if ($ticket->getWidgetId()): ?><tr><th>Widget</th><td><?php echo $ticket->getWidgetId() ?></td></tr><?php endif ?>
          <?php if ($ticket->getTargetListId()): ?><tr><th>Target-list</th><td><?php echo $ticket->getTargetList()->getName() ?></td></tr><?php endif ?>
          <tr><td colspan="2"><blockquote style="white-space:pre-line"><?php echo $ticket->getText() ?></blockquote></td></tr>
        </tbody>
      </table>
    </div>
    <div class="modal-footer">
      <?php if (isset($csrf_token)): ?>
      <form class="ajax_form bottom0" method="post" action="<?php echo url_for('ticket_action') ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>" />
        <input type="hidden" value="<?php echo $ticket->getId() ?>" name="ids[]" />
        <?php if (isset($campaign_id)): ?><input type="hidden" name="campaign_id" value="<?php echo $campaign_id ?>" /><?php endif ?>
        <?php if (isset($petition_id)): ?><input type="hidden" name="petition_id" value="<?php echo $petition_id ?>" /><?php endif ?>
        
        <a class="btn btn-success submit" data-submit='{"method": "approve", "view": "close" }'>Approve</a>
        <a class="btn btn-danger submit" data-submit='{"method": "decline", "view": "close" }'>Decline</a>
        <a class="btn" data-dismiss="modal">Close</a>
      </form>
      <?php else: ?>
        <a class="btn" data-dismiss="modal">Close</a>
      <?php endif ?>
    </div>
  </form>
</div>