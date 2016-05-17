<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * dashboard action actions.
 *
 * @author     Martin
 */
class d_actionActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $this->includeChosen();
  }

  public function executeByCampaign(sfWebRequest $request) {
    $id = $request->getGetParameter('id');
    $target = $request->getGetParameter('target');
    if (!$target && !is_string($target)) {
      return $this->ajax()->alert('invalid')->render();
    }

    if (empty($id)) {
      return $this->ajax()
          ->empty_($target)
          ->append($target, '<option value="">select action</option>')
          ->trigger($target, 'chosen:updated')
          ->render();
    }

    if (!$id && !is_numeric($id)) {
      return $this->ajax()->alert('invalid')->render();
    }

    $campaign = CampaignTable::getInstance()->findById($id);
    if (!$campaign) {
      return $this->ajax()->alert('campaign not found')->render();
    }

    if (!$this->getGuardUser()->isCampaignMember($campaign)) {
      return $this->ajax()->alert('no rights')->render();
    }

    $this->ajax()
      ->empty_($target)
      ->append($target, '<option value="">select action</option>');

    foreach (PetitionTable::getInstance()->queryByCampaign($campaign)->execute() as $petition) { /* @var $petition Petition */
      $this->ajax()->append($target, sprintf('<option value="%s">%s</option>', $petition->getId(), htmlentities($petition->getName(), ENT_COMPAT, 'UTF-8')));
    }

    return $this->ajax()->trigger($target, 'chosen:updated')->render();
  }

  public function executePager(sfWebRequest $request) {
    $page = $request->getParameter('page', 1);

    if ($request->hasParameter('id')) {
      $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
      /* @var $campaign Campaign */
      if (!$campaign) {
        return $this->notFound();
      }

      if (!$this->getGuardUser()->isCampaignMember($campaign)) {
        return $this->noAccess();
      }

      return $this->ajax()->replaceWithComponent('#action_list', 'd_action', 'list', array('page' => $page, 'campaign' => $campaign, 'no_filter' => true))->render();
    }

    return $this->ajax()->replaceWithComponent('#action_list', 'd_action', 'list', array('page' => $page, 'no_filter' => true))->render();
  }

  public function executeJoin(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#action_list table', 'after');

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('action_join')) {
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();
    }

    $id = $request->getPostParameter('id');
    if (!is_numeric($id)) {
      return $this->ajax()->alert('invalid data', 'Error')->render();
    }

    $petition = PetitionTable::getInstance()->findById($id);
    /* @var $petition Petition */
    if (!$petition) {
      return $this->ajax()->alert('Petition not found', 'Error')->render();
    }

    $pr = $this->getGuardUser()->getRightsByPetition($petition);

    if ($pr && $pr->getActive() && $pr->getMember()) {
      return $this->ajax()->alert('You are already action member', '')->render();
    }

    if ($this->getGuardUser()->isCampaignAdmin($petition->getCampaignId())) {
      if (!$pr) {
        $pr = new PetitionRights();
        $pr->setUserId($this->getGuardUser()->getId());
        $pr->setPetitionId($petition->getId());
      }
      $pr->setActive(1);
      $pr->setMember(1);
      $pr->save();

      return $this->ajax()->alert('Directly joined because you are Campaign-Admin', '')->render();
    }

    $ticket = TicketTable::getInstance()->generate(array(
        TicketTable::CREATE_AUTO_FROM => true,
        TicketTable::CREATE_PETITION => $petition,
        TicketTable::CREATE_KIND => TicketTable::KIND_JOIN_PETITION,
        TicketTable::CREATE_CHECK_DUPLICATE => true,
    ));
    if ($ticket) {
      $ticket->save();
      $ticket->notifyAdmin();
    } else {
      return $this->ajax()->alert('Application already pending', '')->render();
    }

    return $this->ajax()->alert('Application has been sent to Campaign admin', '')->render();
  }

  public function executeLeave(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#action_list table', 'after');

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('action_leave')) {
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();
    }

    $id = $request->getPostParameter('id');
    if (!is_numeric($id)) {
      return $this->ajax()->alert('invalid data', 'Error')->render();
    }

    $petition = PetitionTable::getInstance()->findById($id);
    /* @var $petition Petition */
    if (!$petition) {
      return $this->ajax()->alert('Petition not found', 'Error')->render();
    }

    $pr = $this->getGuardUser()->getRightsByPetition($petition);
    /* @var $pr PetitionRights */
    if ($pr) {
      if ($pr->getActive()) {
        $pr->setActive(0);
        $pr->save();
      }

      return $this->ajax()
          ->replaceWithComponent('#action_list', 'd_action', 'list', $request->getPostParameter('campaign') ? array('campaign' => $petition->getCampaign()) : null)
          ->alert('Left action ' . $pr->getPetition()->getName(), '')->render();
    }

    return $this->ajax()->alert('You are not editor of this Action', 'Error')->render();
  }

  private function canCreateCamapaign() {
    return $this->getGuardUser()->hasPermission(myUser::CREDENTIAL_ADMIN) || StoreTable::value(StoreTable::CAMAPIGN_CREATE_ON);
  }

  public function executeNew(sfWebRequest $request) {
    if (!$this->getGuardUser()->hasCampaigns() && !$this->canCreateCamapaign()) {
      $this->redirect($this->getContext()->getRouting()->generate('dashboard', array(), true) . '?no_campaign=1');
    }

    $petition = new Petition();
    $petition->setHomepage(1);
    $petition->setWithCountry(1);
    $petition->setKind(Petition::KIND_PETITION);
    $petition->setStartAt(gmdate('Y-m-d'));
    $petition->setEndAt(gmdate('Y-m-d', strtotime('next year')));
    $petition->setFromName($this->getGuardUser()->getOrganisation() ? : $this->getGuardUser()->getName());
    $petition->setFromEmail($this->getGuardUser()->getEmailAddress());
    $petition->setPolicyCheckbox(PetitionTable::POLICY_CHECKBOX_NO);

    $campaign_id = $request->getGetParameter('campaign');
    if (is_numeric($campaign_id)) {
      $campaign = CampaignTable::getInstance()->findById($campaign_id, $this->userIsAdmin());
      if ($campaign) {
        $petition->setCampaign($campaign);
      }
    }

    $this->form = new NewPetitionForm($petition, array(NewPetitionForm::OPTION_USER => $this->getGuardUser()));

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $con = PetitionTable::getInstance()->getConnection();
        $con->beginTransaction();
        try {
          $this->form->save();
          $petition = $this->form->getObject();
          $pr = new PetitionRights();
          $pr->setPetition($petition);
          $pr->setUser($this->getGuardUser());
          $pr->setActive(1);
          $pr->setAdmin(0);
          $pr->setMember(1);
          $pr->save();

          $con->commit();
        } catch (Exception $e) {
          $con->rollback();
        }

        return $this->ajax()->redirectRotue('petition_edit_', array('id' => $petition->getId()))->render();
      } else {
        return $this->ajax()->form($this->form)->render();
      }
    }

    $this->includeChosen();
  }

  public function executeOverview(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$petition->isEditableBy($this->getGuardUser())) {
      return $this->noAccess();
    }

    $this->petition = $petition;
  }

  public function executeEdit(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$petition->isEditableBy($this->getGuardUser())) {
      return $this->noAccess();
    }

    $form = new EditPetitionForm($petition, array(EditPetitionForm::USER => $this->getGuardUser()));

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()), $request->getFiles($form->getName()));

      if ($form->isValid()) {
        $con = PetitionTable::getInstance()->getConnection();
        $con->beginTransaction();
        try {
          $form->save();

          $con->commit();
        } catch (Exception $e) {
          $con->rollback();
        }
        if ($request->getPostParameter('go_translation')) {
          return $this->ajax()->redirectRotue('translation_create', array('id' => $petition->getId()))->render();
        } elseif ($request->getPostParameter('go_pledge')) {
          return $this->ajax()->redirectRotue('pledge_list', array('id' => $petition->getId()))->render();
        } elseif ($request->getPostParameter('go_target')) {
          return $this->ajax()->redirectRotue('target_petition_edit', array('id' => $petition->getId()))->render();
        } if (!$petition->getPetitionText()->count()) {
          return $this->ajax()->redirectRotue('translation_create', array('id' => $petition->getId()))->render();
        } else {
          return $this->ajax()->redirectRotue('petition_overview', array('id' => $petition->getId()))->render();
        }
      } else {
        if ($form->hasDeleteStatus()) { // ignore form errors when action should be deleted
          $tv = $form->getTaintedValues();
          if (is_array($tv) && isset($tv['status']) && $tv['status'] == Petition::STATUS_DELETED) {
            if (!$form->getErrorSchema()->offsetExists(EditPetitionForm::getCSRFFieldName())) {
              $petition->setStatus(Petition::STATUS_DELETED);
              $petition->save();

              return $this->ajax()->redirectRotue('petition_overview', array('id' => $petition->getId()))->render();
            }
          }
        }

        return $this->ajax()->form($form)->render();
      }
    }

    $this->form = $form;
    $this->includeIframeTransport();
    $this->includeChosen();
  }

  public function executeEditTarget(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$petition->isEditableBy($this->getGuardUser())) {
      return $this->noAccess();
    }

    $form = new EditPetitionTargetForm($petition, array(EditPetitionForm::USER => $this->getGuardUser()));
    $form->bind($request->getPostParameter($form->getName()), $request->getFiles($form->getName()));

    if ($form->isValid()) {
      $form->save();

      if ($request->getPostParameter('go_translation')) {
        return $this->ajax()->redirectRotue('translation_create', array('id' => $petition->getId()))->render();
      } elseif ($request->getPostParameter('go_pledge')) {
        return $this->ajax()->redirectRotue('pledge_list', array('id' => $petition->getId()))->render();
      } elseif ($request->getPostParameter('edit_target')) {
        return $this->ajax()->redirectRotue('target_petition_edit', array('id' => $petition->getId()), array('e' => 1))->render();
      } else {
        return $this->ajax()->redirectRotue('target_petition_edit', array('id' => $petition->getId()))->render();
      }
    }
  }

  public function executeTarget(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$petition->isEditableBy($this->getGuardUser())) {
      return $this->noAccess();
    }

    $value = $request->getPostParameter('value');
    if (!is_numeric($value)) {
      $value = null;
    }

    if ($value) {
      $ml = MailingListTable::getInstance()->queryByCampaignForUser($petition->getCampaign(), $this->getGuardUser(), $petition->getMailingListId() ? $petition->getMailingList() : null, false, $value)->fetchOne();
      if (!$ml) {
        return $this->notFound();
      }

      $target_choices = $ml->getTargetChoices();
    } else {
      $target_choices = array();
    }
    $html = '';
    foreach ($target_choices as $key => $name) {
      $html .= sprintf('<option value="%s">%s</option>', $key, htmlentities($name, ENT_COMPAT, 'UTF-8'));
    }

    return $this->ajax()
        ->html('#edit_petition_target_target_selector_1', $html)->trigger('#edit_petition_target_target_selector_1', 'chosen:updated')
        ->html('#edit_petition_target_target_selector_2', $html)->trigger('#edit_petition_target_target_selector_2', 'chosen:updated')
        ->remove('#edit, #edit-btn')
        ->show('#edit-btn-save')
        ->render();
  }

  public function executeEditMembers(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#petition_members', 'after');

    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->ajax()->alert('Action not found', 'Error')->render();
    }

    if (!$petition->isCampaignAdmin($this->getGuardUser())) {
      return $this->ajax()->alert('You are not admin', 'Error')->render();
    }

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('action_members')) {
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();
    }

    $ids = $request->getPostParameter('ids');
    $method = $request->getPostParameter('method');
    if (!in_array($method, array('block', 'member'))) {
      return $this->ajax()->alert('Something is wrong.', 'Error')->render();
    }
    $self = false;
    if (is_array($ids)) {
      foreach (PetitionRightsTable::getInstance()->queryByPetitionAndUsers($petition->getId(), $ids)->execute() as $petition_rights) {
        /* @var $petition_rights PetitionRights */
        if ($this->isSelfUser($petition_rights->getUserId())) {
          $self = true;
          continue;
        }

        if ($method === 'block') {
          $petition_rights->setActive(0);
        } elseif ($method === 'member') {
          $petition_rights->setActive(1);
          $petition_rights->setMember(1);
        }

        $petition_rights->save();
      }
    }

    $this->ajax()->replaceWithComponent('#petition_members', 'd_action', 'members', array('petition' => $petition));

    if ($self) {
      $this->ajax()->alert('You can not edit yourself.', 'Error');
    }

    return $this->ajax()->render();
  }

  public function executeTranslations(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$petition->isEditableBy($this->getGuardUser())) {
      return $this->noAccess();
    }

    $this->translations = $petition->getPetitionText();

    $this->petition = $petition;

    $this->can_not_create_widget_from_draft = $request->getGetParameter('a');
  }

  public function executeTranslationEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    $copy = null;
    if (isset($route_params['new'])) {
      $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
      /* @var $petition Petition */
      if (!$petition) {
        return $this->notFound();
      }

      $translation = new PetitionText();
      $translation->setPetition($petition);

      $copy_id = $request->getGetParameter('copy');
      if (is_numeric($copy_id)) {
        $copy = PetitionTextTable::getInstance()->find($copy_id);
        /* @var $copy PetitionText */
        if ($copy && $copy->getPetitionId() != $petition->getId()) {
          $copy = null;
        }
      }
    } else {
      $translation = PetitionTextTable::getInstance()->find($request->getParameter('id'));
      /* @var $translation PetitionText */
      if (!$translation) {
        return $this->notFound();
      }

      $petition = $translation->getPetition();
      if ($petition->getStatus() == Petition::STATUS_DELETED && !$this->userIsAdmin()) {
        return $this->notFound();
      }
      if (!$this->userIsAdmin() && $petition->getCampaign()->getStatus() == CampaignTable::STATUS_DELETED) {
        return $this->notFound();
      }
    }

    if (!$petition->isEditableBy($this->getGuardUser())) {
      return $this->noAccess();
    }

    $this->form = new TranslationForm($translation, array('copy' => $copy));

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->form->save();

        if ($request->getPostParameter('go_translation')) {
          if ($translation->getStatus() == PetitionText::STATUS_ACTIVE) {
            return $this->ajax()->redirectPostRoute('widget_create', array('id' => $petition->getId()), array('page' => 1, 'lang' => $translation->getId()))->render();
          } else {
            return $this->ajax()->redirectRotue('petition_translations', array('id' => $petition->getId()), array('a' => 1))->render();
          }
        }

        return $this->ajax()->redirectRotue('petition_translations', array('id' => $petition->getId()))->render();
      }

      return $this->ajax()->form($this->form)->render();
    }

    $this->translation = $translation;
    $this->petition = $petition;

    $this->includeMarkdown();
    $this->includeHighlight();
  }

  public function executeTranslationDefaultText(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    if (!$campaign) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isCampaignMember($campaign)) {
      return $this->noAccess();
    }

    $form = new TranslationForm();
    $form_name = $form->getName();

    $value = $request->getGetParameter('value');
    if (!is_string($value)) {
      return $this->notFound();
    }

    $language = LanguageTable::getInstance()->find($value);
    if (!$language) {
      return $this->notFound();
    }

    $validation_email = StoreTable::getInstance()->findByKeyAndLanguageCached(StoreTable::SIGNING_VALIDATION_EMAIL, $value);
    if ($validation_email) {
      $this->ajax()->val('#' . $form_name . '_email_validation_subject', $validation_email->getField('subject', ''));
      $this->ajax()->val('#' . $form_name . '_email_validation_body', $validation_email->getField('body', ''));
    }

    $thankyou_email = StoreTable::getInstance()->findByKeyAndLanguageCached(StoreTable::SIGNING_THANK_YOU_EMAIL, $value);
    if ($thankyou_email) {
      $this->ajax()->val('#' . $form_name . '_thank_you_email_subject', $thankyou_email->getField('subject', ''));
      $this->ajax()->val('#' . $form_name . '_thank_you_email_body', $thankyou_email->getField('body', ''));
    }

    $tellyourfriend_email = StoreTable::getInstance()->findByKeyAndLanguageCached(StoreTable::ACTION_TELL_YOUR_FRIEND_EMAIL, $value);
    if ($tellyourfriend_email) {
      $this->ajax()->val('#' . $form_name . '_email_tellyour_subject', $tellyourfriend_email->getField('subject', ''));
      $this->ajax()->val('#' . $form_name . '_email_tellyour_body', $tellyourfriend_email->getField('body', ''));
    }

    $default_campaign_privacy = CampaignStoreTable::getInstance()->findByCampaignLanguageKey($campaign, $language, CampaignStoreTable::KEY_PRIVACY_POLICY);
    if ($default_campaign_privacy) {
      $this->ajax()->val('#' . $form_name . '_privacy_policy_body', $default_campaign_privacy->getValue());
    } else {
      $privacy = StoreTable::getInstance()->findByKeyAndLanguageCached(StoreTable::ACTION_PRIVACY_POLICY, $value);
      if ($privacy) {
        $this->ajax()->val('#' . $form_name . '_privacy_policy_body', $privacy->getField('body', ''));
      }
    }

    return $this->ajax()->render();
  }

  public function executeTodo(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$petition->isCampaignAdmin($this->getGuardUser())) {
      return $this->noAccess();
    }

    $this->petition = $petition;
  }

  public function executeData(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isPetitionMember($petition, true)) {
      return $this->noAccess();
    }

    $route_params = $this->getRoute()->getParameters();
    $this->subscriptions = isset($route_params['type']) && $route_params['type'] === 'email';

    $this->petition = $petition;

    $this->includeChosen();
  }

  public function executeSpf(sfWebRequest $request) {
    $mail = $request->getPostParameter('email');
    if (is_string($mail)) {
      $mail = trim($mail);

      if (!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', $mail)) {
        $mail = null;
      }
    } else {
      $mail = null;
    }

    if ($mail) {
      $status = UtilSpf::query($mail);

      $this->ajax()->appendPartial('body', 'spf', array(
          'status' => UtilSpf::$STATUS[$status],
          'text' => UtilSpf::$STATUS_TEXT[$status],
          'ip' => sfConfig::get('app_spf_ip'),
      ))->modal('#spf_modal');
    }

    return $this->ajax()->render();
  }

  public function executeEditFollow(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#petition_follow', 'after');

    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->ajax()->alert('Action not found', 'Error')->render();
    }

    if (!$petition->isCampaignAdmin($this->getGuardUser())) {
      return $this->ajax()->alert('You are not admin', 'Error')->render();
    }

    $form = new EditPetitionFollowForm($petition);
    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $form->save();
        if (!$form->getValue('follow_petition_id')) {
          $this->ajax()->remove('#alert-forward-info');
        }

        return $this->ajax()
            ->alert('Saved.', '')
            ->replaceWithPartial('#petition_edit_form', 'form', array(
                'form' => new EditPetitionForm($petition, array(EditPetitionForm::USER => $this->getGuardUser()))
            ))
            ->render();
      }

      return $this->ajax()->form($form)->render();
    } else {
      return $this->forward404();
    }
  }

  public function executeHardDelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $petition = PetitionTable::getInstance()->findById($id, true, false);
      /* @var $petition Petition */
      if (!$petition || $petition->getStatus() != Petition::STATUS_DELETED) {
        return $this->notFound();
      }
    }

    $csrf_token = UtilCSRF::gen('delete_petition', $petition->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#petition_delete_modal .modal-body')->render();
      }

      $this->ajax()->redirectRotue('campaign_edit_', array('id' => $petition->getCampaignId()));
      $petition->delete();
      return $this->ajax()->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $id, 'name' => $petition->getName(), 'csrf_token' => $csrf_token))
        ->modal('#petition_delete_modal')
        ->render();
  }

}
