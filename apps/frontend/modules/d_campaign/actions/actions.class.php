<?php

/**
 * dashboard campaign actions.
 *
 * @package    policat
 * @subpackage d_campaign
 * @author     Martin
 */
class d_campaignActions extends policatActions {

  public function executeList(sfWebRequest $request) {
    $this->campaigns = CampaignTable::getInstance()->queryAll()->execute();
  }

  public function executeCreate(sfWebRequest $request) {
    if (!($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) || StoreTable::value(StoreTable::CAMAPIGN_CREATE_ON))) {
      return $this->ajax()->alert('', 'You have no right to create a campaign.', '#my_campaigns h3', 'after')->render();
    }

    $form = new NewCampaignNameForm();
    $form->bind($request->getPostParameter($form->getName()));
    if (!$form->isValid())
      return $this->ajax()->form_error_list($form, '#my_campaigns_create', 'after')->render();

    CampaignTable::getInstance()->getConnection()->beginTransaction();
    try {
      $campaign = new Campaign();
      $campaign->setName($form->getValue('name'));
      $campaign->setDataOwner($this->getGuardUser());
      $store = StoreTable::getInstance()->findByKey(StoreTable::PRIVACY_AGREEMENT);
      if ($store)
        $campaign->setPrivacyPolicy($store->getField('text'));
      $campaign->save();

      $cr = new CampaignRights();
      $cr->setCampaign($campaign);
      $cr->setUser($this->getGuardUser());
      $cr->setActive(1);
      $cr->setAdmin(1);
      $cr->setMember(1);
      $cr->save();

      CampaignTable::getInstance()->getConnection()->commit();
    } catch (Exception $e) {
      CampaignTable::getInstance()->getConnection()->rollback();
      return $this->ajax()->alert('', 'DB Error', '#my_campaigns_create', 'after')->render();
    }

    return $this->ajax()->redirectRotue('campaign_edit_', array('id' => $campaign->getId()))->render();
//    return $this->ajax()->alert('Campaign created', '', '#my_campaigns_create', 'after')->render();
  }

  public function executeJoin(sfWebRequest $request) {
    $form = new SelectCampaignForm(array(), array(SelectCampaignForm::NAME => 'select_join_campaign'));
    $form->bind($request->getPostParameter($form->getName()));
    if (!$form->isValid())
      return $this->ajax()->alert('please select a campaign', 'Error', '#my_campaigns_join', 'after')->render();

    $campaign = CampaignTable::getInstance()->findById($form->getValue('id'), $this->userIsAdmin());
    if (!$campaign)
      return $this->ajax()->alert('Campaign not found', 'Error', '#my_campaigns_join', 'after')->render();

    $cr = $this->getGuardUser()->getRightsByCampaign($campaign);
    if ($cr && $cr->getActive() && $cr->getMember())
      return $this->ajax()->alert('You are already campaign member', '', '#my_campaigns_join', 'after')->render();

    $ticket = TicketTable::getInstance()->generate(array(
        TicketTable::CREATE_AUTO_FROM => true,
        TicketTable::CREATE_CAMPAIGN => $campaign,
        TicketTable::CREATE_KIND => TicketTable::KIND_JOIN_CAMPAIGN,
        TicketTable::CREATE_CHECK_DUPLICATE => true
    ));

    if ($ticket) {
      $ticket->save();
      $ticket->notifyAdmin();
    } else
      return $this->ajax()->alert('Application already pending', '', '#my_campaigns_join', 'after')->render();

    return $this->ajax()->alert('Application has been sent to Campaign admin', '', '#my_campaigns_join', 'after')->render();
  }

  public function executeLeave(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $campaign = CampaignTable::getInstance()->findById($id);
      /* @var $campaign Campaign */
      if (!$campaign)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('leave_campaign', $campaign->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#campaign_leave_modal .modal-body', 'append')->render();

      $cr = $this->getGuardUser()->getRightsByCampaign($campaign);
      if ($cr) {
        if ($cr->getAdmin())
          return $this->ajax()->alert('You can not leave a campaign as admin', 'Error', '#campaign_leave_modal .modal-body', 'append')->render();

        if ($cr->getActive()) {
          $cr->setActive(0);
          $cr->save();
        }
        return $this->ajax()->redirectRotue('dashboard')->render();
      }
      
      return $this->ajax()->alert('not member of campaign ', 'Error', '#campaign_leave_modal .modal-body', 'append')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'leave', array('id' => $id, 'name' => $campaign->getName(), 'csrf_token' => $csrf_token))
        ->modal('#campaign_leave_modal')
        ->render();

  }

  public function executeGoEdit(sfWebRequest $request) {
    $form = new SelectCampaignForm(array(), array(SelectCampaignForm::NAME => 'select_edit_campaign'));
    $form->bind($request->getPostParameter($form->getName()));
    if (!$form->isValid())
      return $this->ajax()->alert('please select a campaign', 'Error', '#campaign_admin_go_edit', 'after')->render();

    $campaign = CampaignTable::getInstance()->findById($form->getValue('id'), $this->userIsAdmin());
    if (!$campaign)
      return $this->ajax()->alert('Campaign not found', 'Error', '#campaign_admin_go_edit', 'after')->render();

    return $this->ajax()->redirectRotue('campaign_edit_', array('id' => $campaign->getId()))->render();
  }

  public function executeEdit(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign)
      return $this->notFound();

    if (!$this->getGuardUser()->isCampaignMember($campaign))
      return $this->noAccess();

    $this->campaign = $campaign;
    $this->admin = $campaign->isEditableBy($this->getGuardUser());
    $this->hasResign = TicketTable::getInstance()->queryResignTicketForCampaign($campaign)->fetchOne();

    $this->includeChosen();
  }

  public function executeEditMembers(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#campaign_members', 'append');

    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign)
      return $this->ajax()->alert('Campaign not found', 'Error')->render();

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('revoke', $campaign->getId()))
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();

    if (!$campaign->isEditableBy($this->getGuardUser()))
      return $this->ajax()->alert('You are not admin of this campaign', 'Error')->render();

    $ids = $request->getPostParameter('ids');
    $method = $request->getPostParameter('method');
    if (!in_array($method, array('block', 'member', 'admin')))
      return $this->ajax()->alert('Something is wrong.', 'Error')->render();
    $self_message = '';
    if (is_array($ids)) {
      foreach (CampaignRightsTable::getInstance()->queryByCampaignAndUsers($campaign->getId(), $ids)->execute() as $campaign_rights) {
        /* @var $campaign_rights CampaignRights */
        if ($this->isSelfUser($campaign_rights->getUserId())) {
          if ($method === 'enable')
            $campaign_rights->setActive(1);
          elseif ($method === 'block') {
            $self_message = 'You can not block yourself.';
          } elseif ($method === 'member') {
            $self_message = 'You can not revoke your own admin-status.';
          } elseif ($method === 'admin') {
            $campaign_rights->setActive(1);
            $campaign_rights->setMember(1);
            $campaign_rights->setAdmin(1);
          }
        } else {
          if ($method === 'block') {
            $campaign_rights->setActive(0);
          } elseif ($method === 'member') {
            $campaign_rights->setActive(1);
            $campaign_rights->setMember(1);
            $campaign_rights->setAdmin(0);
          } elseif ($method === 'admin') {
            $campaign_rights->setActive(1);
            $campaign_rights->setMember(1);
            $campaign_rights->setAdmin(1);
          }
        }

        $campaign_rights->save();
      }
    }
    $this->ajax()->replaceWithComponent('#campaign_members', 'd_campaign', 'members', array('campaign' => $campaign));
    if ($self_message)
      $this->ajax()->alert($self_message);

    return $this->ajax()->render();
  }

  public function executeEditSwitches(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign)
      return $this->ajax()->alert('Campaign not found', 'Error', '#campaign_switches', 'append')->render();

    if (!$campaign->isEditableBy($this->getGuardUser()))
      return $this->ajax()->alert('You are not admin of this campaign', 'Error')->render();

    $form = new CampaignSwitchesForm($campaign);
    $form->bind($request->getPostParameter($form->getName()));

    if ($form->isValid()) {
      $form->save();
      return $this->ajax()->render();
    } else
      return $this->ajax()->alert('Invalid data', 'Error', '#campaign_switches', 'append')->render();
  }

  protected function editSomething(sfWebRequest $request, $form_class, $dom_id, $partial) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign)
      return $this->ajax()->alert('Campaign not found', 'Error')->render();

    if (!$campaign->isEditableBy($this->getGuardUser()))
      return $this->ajax()->alert('You are not admin of this campaign', 'Error')->render();

    $form = new $form_class($campaign);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $form->save();
        return $this->ajax()
            ->modal('#' . $dom_id, 'hide')
            ->redirectRotue('campaign_edit_', array('id' => $campaign->getId()))
            ->render();
      } else
        return $this->ajax()->form_error_list($form, '#' . $dom_id . ' .modal-body')->render();
    } else {
      return $this->ajax()
          ->appendPartial('body', $partial, array('form' => $form))
          ->modal('#' . $dom_id)
          ->render();
    }
  }

  public function executeEditName(sfWebRequest $request) {
    return $this->editSomething($request, 'EditCampaignNameForm', 'campaign_name_modal', 'editName');
  }

  public function executeEditPrivacy(sfWebRequest $request) {
    return $this->editSomething($request, 'EditCampaignPrivacyForm', 'campaign_privacy_modal', 'editPrivacy');
  }

  public function executeEditAddress(sfWebRequest $request) {
    return $this->editSomething($request, 'EditCampaignAddressForm', 'campaign_address_modal', 'editAddress');
  }

  private function removeOldResignAndCallTickets(Campaign $campaign) {
    foreach (TicketTable::getInstance()->queryResignTicketForCampaign($campaign)->execute() as $old_ticket) {
      /* @var $old_ticket Ticket */
      $old_ticket->setStatus(TicketTable::STATUS_DENIED);
      $old_ticket->save();
    };

    foreach (TicketTable::getInstance()->queryCallTicketForCampaign($campaign)->execute() as $old_ticket) {
      /* @var $old_ticket Ticket */
      $old_ticket->setStatus(TicketTable::STATUS_DENIED);
      $old_ticket->save();
    };
  }

  public function executeResignDataOfficer(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign)
      return $this->ajax()->alert('Campaign not found', 'Error')->render();

    if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_SYSTEM) && !$this->getGuardUser()->isDataOwnerOfCampaign($campaign))
      return $this->ajax()->alert('You do not have the rights.', 'Error')->render();

    $form = new ResignDataOfficerForm(array(), array(ResignDataOfficerForm::OPTION_CAMPAIGN => $campaign));

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $new_id = $form->getValue('new'); /* @var $new sfGuardUser */
        if (!$campaign->getDataOwnerId() || $campaign->getDataOwnerId() != $new_id) {
          if ($this->getUser()->hasCredential(myUser::CREDENTIAL_SYSTEM)) {
            $this->removeOldResignAndCallTickets($campaign);
            $campaign->setDataOwnerId($new_id);
            $campaign->save();
          } else {
            $ticket = TicketTable::getInstance()->generate(array(
                TicketTable::CREATE_AUTO_FROM => true,
                TicketTable::CREATE_CAMPAIGN => $campaign,
                TicketTable::CREATE_CHECK_DUPLICATE => true,
                TicketTable::CREATE_KIND => TicketTable::KIND_RESIGN_DATA_OFFICER,
                TicketTable::CREATE_TO => sfGuardUserTable::getInstance()->find($new_id)
            ));

            if ($ticket) {
              $con = TicketTable::getInstance()->getConnection();
              $con->beginTransaction();
              try {
                $this->removeOldResignAndCallTickets($campaign);
                $ticket->save();
                $ticket->notifyAdmin();

                $con->commit();
              } catch (Exception $e) {
                $con->rollback();
                return $this->ajax()->alert('Transactional error', 'Error')->render();
              }
            } else {
              // duplicate
            }
          }
        } else if ($campaign->getDataOwnerId() == $new_id) {
          $this->removeOldResignAndCallTickets($campaign);
        }

        return $this->ajax()
            ->modal('#resign_data_officer_modal', 'hide')
            ->redirectRotue('campaign_edit_', array('id' => $campaign->getId()))
            ->render();
      } else
        return $this->ajax()->form_error_list($form, '#resign_data_officer_modal .modal-body')->render();
    } else {
      return $this->ajax()
          ->appendPartial('body', 'resignDataOfficer', array('form' => $form, 'campaign' => $campaign))
          ->modal('#resign_data_officer_modal')
          ->render();
    }
  }

  public function executeCallDataOfficer(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign)
      return $this->ajax()->alert('Campaign not found', 'Error')->render();

    if (!$this->getGuardUser()->isCampaignAdmin($campaign))
      return $this->ajax()->alert('You do not have the rights.', 'Error')->render();

    if (!$campaign->getDataOwnerId())
      return $this->ajax()->alert('Campaign has no Data protection officer.', 'Error')->render();

    $officer = $campaign->getDataOwner();

    $ticket = TicketTable::getInstance()->generate(array(
        TicketTable::CREATE_AUTO_FROM => true,
        TicketTable::CREATE_CAMPAIGN => $campaign,
        TicketTable::CREATE_KIND => TicketTable::KIND_CALL_DATA_OFFICER,
        TicketTable::CREATE_CHECK_DUPLICATE => true,
        TicketTable::CREATE_TO => $officer,
    ));
    if ($ticket) {
      $ticket->save();
      $ticket->notifyAdmin();
    }

    return $this->ajax()
        ->alert('Request sent to current data protection officer.', '', '#campaign_data_officer')
        ->render();
  }

  public function executeData(sfWebRequest $request) {
    $this->campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $this->campaign Campaign */
    if (!$this->campaign)
      return $this->notFound();

    if (!$this->getGuardUser()->isCampaignAdmin($this->campaign))
      return $this->noAccess();

    $this->includeChosen();
  }

  public function executePrivacyList(sfWebRequest $request) {
    $this->campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $this->campaign Campaign */
    if (!$this->campaign)
      return $this->notFound();

    if (!$this->getGuardUser()->isCampaignAdmin($this->campaign))
      return $this->noAccess();

    $this->languages = LanguageTable::getInstance()->queryAll()->execute();
  }

  public function executePrivacyLang(sfWebRequest $request) {
    $this->campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $this->campaign Campaign */
    if (!$this->campaign)
      return $this->notFound();

    if (!$this->getGuardUser()->isCampaignAdmin($this->campaign))
      return $this->noAccess();

    $this->languages = LanguageTable::getInstance()->queryAll()->execute();

    $this->language = LanguageTable::getInstance()->find($request->getParameter('lang'));
    if (!$this->language)
      return $this->notFound();

    $campaign_store = CampaignStoreTable::getInstance()->findByCampaignLanguageKey($this->campaign, $this->language, CampaignStoreTable::KEY_PRIVACY_POLICY);

    if (!$campaign_store) {
      $campaign_store = new CampaignStore();
      $campaign_store->setCampaign($this->campaign);
      $campaign_store->setLanguage($this->language);
      $campaign_store->setKey(CampaignStoreTable::KEY_PRIVACY_POLICY);

      $store = StoreTable::getInstance()->findByKeyAndLanguage(StoreTable::ACTION_PRIVACY_POLICY, $campaign_store->getLanguage()->getId());
      if (!$store)
        $store = StoreTable::getInstance()->findByKeyAndLanguage(StoreTable::ACTION_PRIVACY_POLICY, 'en');
      if ($store)
        $campaign_store->setValue($store->getField('body'));
    }

    $this->form = new CampaignStoreForm($campaign_store);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $before = $campaign_store->getValue();
        $this->form->save();

        $data_owner = $this->campaign->getDataOwnerId() ? $this->campaign->getDataOwner() : null;
        /* @var $data_owner sfGuardUser */

        if ($data_owner && $this->getGuardUser()->getId() != $data_owner->getId()) {
          $ticket = TicketTable::getInstance()->generate(array(
              TicketTable::CREATE_AUTO_FROM => true,
              TicketTable::CREATE_TO => $data_owner,
              TicketTable::CREATE_CAMPAIGN => $this->campaign,
              TicketTable::CREATE_KIND => TicketTable::KIND_PRIVACY_POLICY_CHANGED,
              TicketTable::CREATE_TEXT => $this->getGuardUser()->getFullName() . ' (' . $this->getGuardUser()->getOrganisation() .
              ") modified the privacy policy text '" . $this->language->getName() . "'\n" .
              "BEFORE:\n" . $before . "\n\nAFTER:\n" . $campaign_store->getValue()
          ));
          $ticket->save();
          $ticket->notifyAdmin();
        }

        return $this->ajax()
            ->remove('#no_text')
            ->alert('Saved.', '', '#campaign_privacy_form .form-actions', 'before')
            ->render();
      }

      return $this->ajax()->form($this->form)->render();
    }

    $this->includeMarkdown();
    $this->includeHighlight();
  }

  public function executeDelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $campaign = CampaignTable::getInstance()->findById($id);
      /* @var $campaign Campaign */
      if (!$campaign)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('delete_campaign', $campaign->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#campaign_delete_modal .modal-body')->render();

      $campaign->setStatus(CampaignTable::STATUS_DELETED);
      $campaign->save();
      return $this->ajax()->redirectRotue('campaign_undelete_list')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $id, 'name' => $campaign->getName(), 'csrf_token' => $csrf_token))
        ->modal('#campaign_delete_modal')
        ->render();
  }

  public function executeUndeleteList(sfWebRequest $request) {
    $this->campaigns = CampaignTable::getInstance()->queryDeleted()->execute();
  }

  public function executeUndelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $campaign = CampaignTable::getInstance()->findById($id, true);
      /* @var $campaign Campaign */
      if (!$campaign)
        return $this->notFound('xx');
    }

    $csrf_token = UtilCSRF::gen('undelete_campaign', $campaign->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#campaign_undelete_modal .modal-body')->render();

      $campaign->setStatus(CampaignTable::STATUS_ACTIVE);
      $campaign->save();
      return $this->ajax()->redirectRotue('campaign_edit_', array('id' => $campaign->getId()))->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'undelete', array('id' => $id, 'name' => $campaign->getName(), 'csrf_token' => $csrf_token))
        ->modal('#campaign_undelete_modal')
        ->render();
  }

}
