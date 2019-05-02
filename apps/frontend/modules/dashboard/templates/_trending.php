<?php
use_helper('Number');
if ($petitions->count()):
  ?>
  <h3>Top trending actions</h3>
  <table class="table table-responsive-md table-bordered table-striped table-condensed">
      <thead>
          <tr>
              <th>E-action</th>
              <th class="span3">in campaign</th>
              <th class="span2 align-right">Widgets</th>
              <th class="span2 align-right">Participants</th>
          </tr>
      </thead>
      <tbody>
          <?php
          foreach ($petitions as $petition): /* @var $petition Petition */
            ?>
            <tr>
                <td><?php $sf_user->linkPetition($petition) ?></td>
                <td><?php $sf_user->linkCampaign($petition->getCampaign()) ?></td>
                <td class="align-right">
                    <?php $sf_user->linkPetition($petition, null, format_number($petition->countWidgets()), null, false, 'petition_widgets') ?>
                </td>
                <td class="align-right">
                    <?php $sf_user->linkPetition($petition, null, format_number($petition->countSignings()), null, false, 'petition_data') ?>
                </td>
            </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  <?php
endif;

if ($widgets->count()):
  ?>
  <h3>Top trending widgets</h3>
  <table class="table table-responsive-md table-bordered table-striped table-condensed">
      <thead>
          <tr>
              <th class="span1">ID</th>
              <th>of e-action</th>
              <th class="span2">URL</th>
              <th class="span2 align-right">Participants</th>
              <th class="span2 align-right">Subscribers</th>
          </tr>
      </thead>
      <tbody>
          <?php
          foreach ($widgets as $widget): /* @var $widget Widget */
            ?>
            <tr>
                <td><?php $sf_user->linkWidget($widget) ?></td>
                <td><?php $sf_user->linkPetition($widget->getPetition()) ?></td>
                <td class="span2 span2-ellipsis">
                    <?php if ($widget->getLastRef()): ?>
                      <a class="add_popover popover_left popover_hover" data-content="<?php echo $widget->getLastRefShy() ?>" target="_blank" href="<?php echo $widget->getLastRef() ?>"><?php echo $widget->getLastRefShort(30) ?></a>
                    <?php endif ?>
                </td>
                <td class="align-right">
                    <?php if ($widget->getDataOwner() && $widget->getUserId() == $sf_user->getGuardUser()->getId()): ?>
                      <a href="<?php echo url_for('widget_data', array('id' => $widget->getId())) ?>">
                          <?php echo format_number($widget->countSignings()) ?>
                      </a>
                    <?php else: ?>
                      <?php echo format_number($widget->countSignings()) ?>
                    <?php endif ?>
                </td>
                <td class="align-right">
                    <?php if ($widget->getDataOwner() && $widget->getUserId() == $sf_user->getGuardUser()->getId()): ?>
                      <a href="<?php echo url_for('widget_data_email', array('id' => $widget->getId())) ?>">
                          <?php echo format_number($widget->countSubscriberSignings()) ?>
                      </a>
                    <?php else: ?>
                      <?php echo format_number($widget->countSubscriberSignings()) ?>
                    <?php endif ?>
                </td>
            </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  <?php
endif;