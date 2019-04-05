<?php
use_helper('Text', 'Number', 'Date');
if (!isset($no_filter)):
  /* @var $petition Petition */
  $url = isset($petition) ? url_for('widget_pager_petition', array('page' => 1, 'id' => $petition->getId())) : url_for('widget_pager', array('page' => 1))
  ?>
  <form method="get" class="form-inline ajax_form filter_form" action="<?php echo $url ?>">
    <?php echo $form ?>
    <button class="btn btn-primary btn-sm mt-3" type="submit">Filter</button>
    <button class="filter_reset btn btn-sm mt-3">Reset filter</button>
  </form>
<?php endif ?>
<div id="widget_list">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID
          <a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_WIDGET_ASC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_WIDGET_ASC ?>">&darr;</a><?php
?><a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_WIDGET_DESC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_WIDGET_DESC ?>">&uarr;</a>
        </th>
        <?php if (!isset($petition)): $show_petition = true ?>
          <?php if (!isset($campaign)): ?>
            <th>Campaign
              <a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_CAMPAIGN_ASC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_CAMPAIGN_ASC ?>">&darr;</a><?php
            ?><a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_CAMPAIGN_DESC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_CAMPAIGN_DESC ?>">&uarr;</a>
            </th>
          <?php endif ?>
          <th>E-action
            <a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_ACTION_ASC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_ACTION_ASC ?>">&darr;</a><?php
          ?><a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_ACTION_DESC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_ACTION_DESC ?>">&uarr;</a>
          </th>
        <?php endif ?>
        <th>Language
          <a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_LANGUAGE_ASC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_LANGUAGE_ASC ?>">&darr;</a><?php
        ?><a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_LANGUAGE_DESC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_LANGUAGE_DESC ?>">&uarr;</a>
        </th>
        <th>Status
          <a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_STATUS_ASC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_STATUS_ASC ?>">&darr;</a><?php
        ?><a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_STATUS_DESC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_STATUS_DESC ?>">&uarr;</a>
        </th>
        <th>Last activity
          <a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_ACTIVITY_ASC) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_ACTIVITY_ASC ?>">&darr;</a><?php
        ?><a class="filter_order <?php if ($form->getValue(WidgetTable::FILTER_ORDER) == WidgetTable::ORDER_ACTIVITY_DESC || !$form->getValue(WidgetTable::FILTER_ORDER)) echo ' active' ?>" data-value="<?php echo WidgetTable::ORDER_ACTIVITY_DESC ?>">&uarr;</a>
        </th>
        <th class="rotate"><div>Participants</div></th>
    <th class="rotate"><div><span title="Signings with verification pending">Pending</span></div></th>
    <th>Owner</th>
    <th><span title="last URL-referer">Embedded<span></th>
          <th>Your rights</th>
          <th></th>
          </tr>
          </thead>
          <tbody>
            <?php
            foreach ($widgets as $widget):
              /* @var $widget Widget */
              $petition = $widget->getPetition();
              $user = $sf_user->getGuardUser()->getRawValue(); /* @var $user sfGuardUser */
              ?>
              <tr>
                <td><?php echo $widget->getId() ?></td>
                <?php if (isset($show_petition)): $trunc_an = truncate_text($petition->getName(), 20, '…'); ?>
                  <?php if (!isset($campaign)): $trunc_cn = truncate_text($petition->getCampaign()->getName(), 15, '…'); ?>
                    <td>
                      <?php $sf_user->linkCampaign($petition->getCampaign(), 15) ?>
                    </td>
                  <?php endif ?>
                  <td>
                      <?php $sf_user->linkPetition($petition, 20) ?>
                  </td>
                <?php endif ?>
                <td><?php echo $widget->getPetitionText()->getLanguage() ?></td>
                <td><?php echo $widget->getStatusName() ?>
                  <?php if ($widget->getPetitionText()->getWidgetId() == $widget->getId()): ?> <span title="featured on portal homepage">(*)</span><?php endif ?>
                </td>
                <td><?php echo format_date($widget->getActivityAt(), 'yyyy-MM-dd') ?></td>
                <td class="align-right"><?php echo format_number($widget->countSignings()) ?></td>
                <td class="align-right"><?php echo format_number($widget->countSigningsPending()) ?></td>
                <td>
                  <?php if ($widget->getUserId()): ?>
                    <a href="mailto:<?php echo $widget->getUser()->getUsername() ?>"><?php echo $widget->getUser()->getFullName() ?></a>
                    <?php if ($widget->getDataOwner() == WidgetTable::DATA_OWNER_YES): ?><br /><span class="label label-info">Data-owner</span><?php endif ?>
                  <?php else: ?>
                    <?php echo $widget->getOrganisation() ?><br />
                    <a href="mailto:<?php echo $widget->getEmail() ?>"><?php echo $widget->getEmail() ?></a>
                  <?php endif ?>
                </td>
                <td><?php if ($widget->getLastRef()): ?>
                    <a target="_blank" title="<?php echo $widget->getLastRef() ?>" href="<?php echo $widget->getLastRef() ?>"><?php echo $widget->getLastRefShort() ?></a>
                  <?php endif ?>
                </td>
                <td>
                  <?php $x = 1; ?>
                  <?php if ($user->isCampaignAdmin($petition->getCampaign()->getRawValue())): $x = 0 ?><span class="label label-important">admin</span><?php endif ?>
                  <?php if ($x && $user->isPetitionMember($petition->getRawValue())): ?><span class="label label-info">editor</span><?php endif ?>
                </td>
                <td>
                  <?php $sf_user->linkWidget($widget, 'edit', 'btn btn-sm btn-primary', true) ?>
                  <?php $follow = false; ?>
                  <?php if ($petition->getFollowPetitionId()):
                    $follows_id = $widget->followsWidgetId($petition->getFollowPetitionId());
                    if ($follows_id): $follow = true; ?>
                      <a class="btn btn-sm btn-info" title="Widget ID <?php echo $follows_id ?>" href="<?php echo url_for('widget_edit', array('id' => $follows_id)) ?>">forwarding to</a>
                    <?php endif ?>
                  <?php endif ?>
                  <?php if ($widget->getStatus() == Widget::STATUS_ACTIVE && !$follow): ?>
                    <a class="btn btn-secondary btn-sm ajax_link" href="<?php echo url_for('widget_view', array('id' => $widget->getId())) ?>">view</a>
                  <?php endif ?>
                  <?php if ($widget->getUserId() && $widget->getUserId() === $sf_user->getUserId()): ?>
                    <?php if ($widget->getDataOwner() == WidgetTable::DATA_OWNER_YES): ?>
                      <a class="btn btn-secondary btn-sm" href="<?php echo url_for('widget_data', array('id' => $widget->getId())) ?>">Participants</a>
                      <a class="btn btn-secondary btn-sm" href="<?php echo url_for('widget_data_email', array('id' => $widget->getId())) ?>">Mailing addresses</a>
                    <?php else: if ($petition->getCampaign()->getOwnerRegister() && !$user->isDataOwnerOfCampaign($petition->getCampaign()->getRawValue())): ?>
                        <a class="btn btn-primary btn-sm ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token, 'id' => $widget->getId())) ?>' href="<?php echo url_for('widget_data_owner') ?>">Become Data-owner</a>
                        <?php
                      endif;
                    endif
                    ?>
                  <?php endif ?>
                  <?php if ($widget->getDataOwner() == WidgetTable::DATA_OWNER_YES && $user->isDataOwnerOfCampaign($petition->getCampaign()->getRawValue())): ?>
                    <a class="btn btn-primary btn-sm ajax_link post" data-submit='<?php echo json_encode(array('csrf_token' => $csrf_token_revoke, 'id' => $widget->getId())) ?>' href="<?php echo url_for('widget_revoke_data') ?>">Re-integrate data</a>
                  <?php endif ?>
                  <?php if ($widget->getUserId() && $user->hasPermission(myUser::CREDENTIAL_ADMIN)): ?><a class="btn btn-secondary btn-sm" href="<?php echo url_for('user_edit', array('id' => $widget->getUserId())) ?>">user account</a><?php endif ?>
                  <?php if (($user->getId() == $widget->getUserId() || $user->isPetitionMember($petition->getRawValue()) || $user->hasPermission(myUser::CREDENTIAL_ADMIN)) && $widget->getOriginWidgetId()): ?>
                  <a class="btn btn-sm btn-info" title="Widget ID <?php echo $widget->getOriginWidgetId() ?>" href="<?php echo url_for('petition_overview', array('id' => $widget->getOriginWidget()->getPetitionId())) ?>">copied from</a>
                  <?php endif ?>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
          </table>
          <?php include_partial('dashboard/pager', array('pager' => $widgets)) ?>
          </div>