<?php $user = $sf_user->getGuardUser(); /* @var $user sfGuardUser */ ?>
<div id="todo">
    <?php if (isset($tickets)): ?>
      <h2>To-Do <a class="btn pull-right" href="<?php echo url_for('dashboard') ?>">reload</a></h2>
      <form class="ajax_form" method="post" action="<?php echo url_for('ticket_action') ?>">
          <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>" />
          <?php $update_params = array(); ?>
          <?php if (isset($campaign_id)): $update_params['campaign_id'] = $campaign_id ?><input type="hidden" name="campaign_id" value="<?php echo $campaign_id ?>" /><?php endif ?>
          <?php if (isset($petition_id)): $update_params['petition_id'] = $petition_id ?><input type="hidden" name="petition_id" value="<?php echo $petition_id ?>" /><?php endif ?>
          <table class="table table-bordered table-head-middle table-condensed table-striped">
              <thead>
                  <tr>
                      <th class="single_check" style="text-align: center"><input type="checkbox" class="checkall" /></th>
                      <th class="approve_decline">
                          <span class="btn-group">
                              <a class="btn btn-success submit <?php if (!$tickets->count()): ?>disabled<?php endif ?>" data-submit='{"method": "approve" }'>Approve</a>
                              <a class="btn btn-danger submit <?php if (!$tickets->count()): ?>disabled<?php endif ?>" data-submit='{"method": "decline" }'>Decline</a>
                          </span>
                      </th>
                      <th>From</th>
                      <?php if (!isset($petition_id)): ?>
                        <th><?php if (!isset($campaign_id)): ?>Campaign / <?php endif ?>E-action</th>
                      <?php endif ?>
                      <th class="span2">Your rights</th>
                  </tr>
              </thead>
              <tbody>
                  <?php if ($tickets->count()): ?>
                    <?php foreach ($tickets as $ticket): /* @var $ticket Ticket */ ?>
                      <tr>
                          <td class="single_check"><input type="checkbox" value="<?php echo $ticket->getId() ?>" name="ids[]" /></td>
                          <td>
                              <?php echo $ticket->getKindName() ?>
                          </td>
                          <td>
                              <?php if ($ticket->getFromId()): ?>
                                <?php echo $ticket->getFrom()->getFullName() ?>
                              <?php endif ?>
                              <?php if ($ticket->getToId()): ?>
                                <span class="label">To</span>
                                <?php echo $ticket->getTo()->getFullName() ?>
                              <?php endif ?>
                          </td>
                          <?php if (!isset($petition_id)): ?>
                            <td>
                                <?php if ($ticket->getCampaignId() && !isset($campaign_id)): ?>
                                  <a href="<?php echo url_for('campaign_edit_', array('id' => $ticket->getCampaignId())) ?>"><?php echo $ticket->getCampaign()->getName() ?></a>
                                <?php endif ?>
                                <?php if ($ticket->getPetitionId()): ?>
                                  <?php if (!isset($campaign_id)): ?>/ <?php endif ?>
                                  <a href="<?php echo url_for('petition_todo', array('id' => $ticket->getPetitionId())) ?>"><?php echo $ticket->getPetition()->getName() ?></a>
                                <?php endif ?>
                                <?php if ($ticket->getWidgetId()): ?>
                                  Widget:
                                  <a href="<?php echo url_for('widget_edit', array('id' => $ticket->getWidgetId())) ?>"><?php echo $ticket->getWidgetId() ?></a>
                                <?php endif ?>
                                <?php if ($ticket->getTargetListId()): ?>
                                  Target-list:
                                  <a href="<?php echo url_for('target_edit', array('id' => $ticket->getTargetListId())) ?>"><?php echo $ticket->getTargetList()->getName() ?></a>
                                <?php endif ?>
                                <?php if ($ticket->getText()): use_helper('Text') ?>
                                  Message: &ldquo;<?php echo truncate_text($ticket->getText()) ?>&rdquo;
                                  <a class="btn btn-mini ajax_link" data-submit='<?php echo json_encode($update_params) ?>' href="<?php echo url_for('ticket_view', array('id' => $ticket->getId())) ?>">read more</a>
                                <?php endif ?>
                            </td>
                          <?php endif ?>
                          <td>
                              <?php if ($ticket->getCampaignId() && $user->isCampaignAdmin($ticket->getCampaignId())): ?>
                                <span class="label label-important">admin</span>
                              <?php endif ?>
                              <?php if ($ticket->getPetitionId() && $user->isPetitionAdmin($ticket->getPetitionId())): ?>
                                <span class="label label-important">member-manager</span>
                              <?php endif ?>
                          </td>
                      </tr>
                      <?php
                    endforeach;
                  else:
                    ?>
                    <tr>
                        <td></td>
                        <td colspan="4"><p style="margin: 5px 0 2px 0; font-style: italic; text-align: center;">Hooray! You have no outstanding tasks!</p></td>
                    </tr>
                  <?php endif;
                  ?>
              </tbody>
          </table>
      </form>
      <?php include_partial('dashboard/pager', array('pager' => $tickets)) ?>
    <?php endif ?>
</div>