<?php $user = $sf_user->getGuardUser(); /* @var $user sfGuardUser */ ?>
<div id="todo">
    <?php if (isset($tickets)): ?>
      <h2>To-Do <a id="ticket_reload" class="btn btn-primary btn-sm pull-right ajax_link" href="<?php echo $tickets->getUrl($tickets->getPage()) ?>">reload</a></h2>

      <?php if ($tickets->count()): ?>
        <?php foreach ($tickets as $ticket): /* @var $ticket Ticket */ ?>
          <?php
          include_component('ticket', 'ticket', array(
              'ticket' => $ticket,
              'campaign_id' => $campaign_id,
              'petition_id' => $petition_id
          ))
          ?>
        <?php endforeach ?>
      <?php else: ?>
        <p>Hooray! You have no outstanding tasks!</p>
      <?php endif ?>

      <?php include_partial('dashboard/pager', array('pager' => $tickets)) ?>
    <?php endif ?>
</div>
