<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class EditPetitionTargetForm extends BasePetitionForm {

  const USER = 'user';

  private $has_delete_status = false;

  public function hasDeleteStatus() {
    return $this->has_delete_status;
  }

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('edit_petition_target[%s]');

    $user = $this->getOption(self::USER, null);
    /* @var $user sfGuardUser */

    $this->useFields(array());

    $this->setWidget('updated_at', new sfWidgetFormInputHidden());
    $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $this->getObject()->getUpdatedAt())));

    if ($this->getObject()->isGeoKind()) {
      $ml_query = MailingListTable::getInstance()
        ->queryByCampaignForUser($this->getObject()->getCampaign(), $user, $this->getObject()->getMailingListId() ? $this->getObject()->getMailingList() : null, false);
      $target_list_objects = $ml_query->execute();
      $group_campaign = 'Select from this campaign';
      $group_campaign_copy = 'Create duplicate of list';
      $group_global = 'Create duplicate of global list';
      $target_lists = array();
      $target_lists['New list'] = array(
          0 => 'Create a new target list'
      );
      $target_list_ids = array(0);

      foreach ($target_list_objects as $target_list) {
        /* @var $target_list MailingList */
        $target_lists[$group_campaign][$target_list->getId()] = $target_list->getName() . ($target_list->getStatus() == MailingListTable::STATUS_DRAFT ? ' [draft]' : '');
        $target_list_ids[] = $target_list->getId();

        $target_lists[$group_campaign_copy]['c' . $target_list->getId()] = 'Copy: ' . $target_list->getName() . ($target_list->getStatus() == MailingListTable::STATUS_DRAFT ? ' [draft]' : '');
        $target_list_ids[] = 'c' . $target_list->getId();
      }

      foreach (MailingListTable::getInstance()->queryGlobalActive()->execute() as $target_list) {
        /* @var $target_list MailingList */

        $target_lists[$group_global]['c' . $target_list->getId()] = 'Global copy: ' . $target_list->getName() . ($target_list->getStatus() == MailingListTable::STATUS_DRAFT ? ' [draft]' : '');
        $target_list_ids[] = 'c' . $target_list->getId();
      }

      $this->setWidget('mailing_list_id', new sfWidgetFormChoice(array(
          'choices' => $target_lists,
          'label' => 'Target-List'
        ), array(
          'class' => 'add_popover ajax_change post span6',
          'data-content' => "Select a target list, for example 'Members of European Parliament'. Please note: you "
          . "might not have rights to use all target lists available: If you select a target list for which don't "
          . "have access rights yet, you will be able to proceed with preparing your action. However, your action"
          . " will remain in \"draft\" status until your campaign admin has approved your request. To create a new "
          . "list for your campaign or use one of the lists that are available for all campaigns, go to the "
          . "'Target list' tab in your campaign settings (go to Dashboard, select your campaign and click 'edit'; "
          . "ask your campaign admin for support).",
          'data-action' => sfContext::getInstance()->getRouting()->generate('petition_target', array('id' => $this->getObject()->getId()))
      )));

      $this->setValidator('mailing_list_id', new sfValidatorChoice(array(
          'choices' => $target_list_ids
      )));

//      if (!$ml_query->copy()->count()) {
//        $this->getWidgetSchema()->setHelp('mailing_list_id', 'You cannot select a target list, because there is no '
//          . 'activated target-list available in your campaign. Go to campaign level and create a new target-list, or '
//          . 'copy an available target-list from the global pool. Make sure, your target-list is activated.');
//      }

      $target_choices = $this->getObject()->getMailingListId() ? $this->getObject()->getMailingList()->getTargetChoices() : array();
      $fields = array();
      $email_targets_json = $this->getObject()->getEmailTargets();
      if (is_string($email_targets_json) && strlen($email_targets_json)) {
        $fields = json_decode($email_targets_json, true);
      }
      $this->setWidget('target_selector_1', new sfWidgetFormChoice(array(
          'choices' => $target_choices,
          'default' => isset($fields[0]) ? $fields[0] : null
        ), array(
          'class' => 'add_popover span6',
          'data-content' => "Activists will be able to select their preferred email recipient from your target list. "
          . "Define, by which criteria activists can filter your list: by country, affiliation or any other criteria "
          . "defined by the \"meta keywords\" in your target-list. In case you run an international action, we "
          . "recommend you to choose the #country# selector, so activists can search for targets in their country. "
          . "Leave this field blank to not offer any filter criteria: then, all targets will be listed with their "
          . "full name.",
      )));
      $this->setValidator('target_selector_1', new sfValidatorString(array('required' => false)));

      $this->setWidget('target_selector_2', new sfWidgetFormChoice(array(
          'choices' => $target_choices,
          'default' => isset($fields[1]) ? $fields[1] : null
        ), array(
          'class' => 'add_popover span6',
          'data-content' => "We recommend you leave this field blank. Then, the second selector will list a number "
          . "of targets by their full name, based on the choice in the first selector. That will allow activists to "
          . "browse your target-list by name of recipient (...and you want your activists to know to whom they are "
          . "sending their emails!)",
      )));
      $this->setValidator('target_selector_2', new sfValidatorString(array('required' => false)));
    }
  }

  public function processValues($values) {
    $values = parent::processValues($values);
    if (isset($values['mailing_list_id'])) {
      $ml_id = $values['mailing_list_id'];
      if ($ml_id) {
        if (strpos($ml_id, 'c') === 0) {
          $ml_id = substr($ml_id, 1);
          $source_target_list = MailingListTable::getInstance()->findById($ml_id, true);
          $copy_target_list = MailingListTable::getInstance()->copy($source_target_list, $this->getObject()->getCampaign(), $source_target_list->getName() . ' copy ' . gmdate('Y-m-d H:i'));

          if ($copy_target_list) {
            $user = $this->getOption(self::USER, null);
            /* @var $user sfGuardUser */
            $tr = new TargetListRights();
            $tr->setMailingListId($copy_target_list->getId());
            $tr->setUserId($user->getId());
            $tr->setActive(1);
            $tr->save();
            $this->getObject()->setMailingList($copy_target_list);
            $values['mailing_list_id'] = $copy_target_list->getId();
          } else {
            throw new Exception('target list copy error');
          }
        } else {
          $this->getObject()->setMailingList(MailingListTable::getInstance()->findById($ml_id, true));
        }
      } else {
        $ml = new MailingList();
        $ml->setCampaignId($this->getObject()->getCampaignId());
        $ml->setStatus(MailingListTable::STATUS_DRAFT);
        $ml->setName($this->getObject()->getName() . ' Target-List ' . gmdate('Y-m-d H:i'));
        $this->getObject()->setMailingList($ml);
      }
    }

    if ($this->getObject()->isGeoKind()) {
      $target_choices = $target_choices = $this->getObject()->getMailingList()->getTargetChoices();
      $set = array();
      foreach (array($this->getValue('target_selector_1'), $this->getValue('target_selector_2')) as $ts) {
        if ($ts && array_key_exists($ts, $target_choices)) {
          $set[] = $ts;
        }
      }

      $values['email_targets'] = json_encode($set);
    }

    return $values;
  }

}
