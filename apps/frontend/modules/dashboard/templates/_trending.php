<?php
use_helper('Number');
if ($petitions->count()):
  ?>
  <h3>Top trending actions</h3>
  <table class="table table-bordered table-striped table-condensed">
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
                <td><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></td>
                <td><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></td>
                <td class="align-right">
                    <a href="<?php echo url_for('petition_widgets', array('id' => $petition->getId())) ?>">
                        <?php echo format_number($petition->countWidgets()) ?>
                    </a>
                </td>
                <td class="align-right">
                    <a href="<?php echo url_for('petition_data', array('id' => $petition->getId())) ?>">
                        <?php echo format_number($petition->countSignings()) ?>
                    </a>
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
  <table class="table table-bordered table-striped table-condensed">
      <thead>
          <tr>
              <th class="span1">ID</th>
              <th>of e-action</th>
              <th class="span2">URL</th>
              <th class="span2 align-right">Participants</th>
          </tr>
      </thead>
      <tbody>
          <?php
          foreach ($widgets as $widget): /* @var $widget Widget */
            ?>
            <tr>
                <td><a class="" href="<?php echo url_for('widget_edit', array('id' => $widget->getId())) ?>"><?php echo $widget->getId() ?></a></td>
                <td><a href="<?php echo url_for('petition_overview', array('id' => $widget->getPetitionId())) ?>"><?php echo $widget->getPetition()->getName() ?></a></td>
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
            </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  <?php
endif;