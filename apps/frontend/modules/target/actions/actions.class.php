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
 * target actions.
 *
 * @package    policat
 * @subpackage target
 * @author     Martin
 */
class targetActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    if ($request->getParameter('id') === '0') {
      if ($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN))
        return;

      return $this->noAccess();
    }

    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign)
      return $this->notFound();

    if (!$this->getGuardUser()->isCampaignMember($campaign))
      return $this->noAccess();

    $this->campaign = $campaign;
  }

  /**
   *
   * @param int $id
   * @return MailingList
   */
  public function findTargetList($id = null) {
    if ($id === null)
      $id = $this->getRequestParameter('id');

    return MailingListTable::getInstance()->findById($id, $this->userIsAdmin());
  }

  public function executePetition(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$petition->isGeoKind()) {
      return $this->notFound();
    }

    if (!$petition->isEditableBy($this->getGuardUser())) {
      return $this->noAccess();
    }

    $target_list_id = $petition->getMailingListId();

    if ($target_list_id) {
      $target_list = $petition->getMailingList();

      if ($this->getGuardUser()->isTargetListMember($target_list, true)) {
        $this->csrf_token = UtilCSRF::gen('target_activate');
        $this->metas = $target_list->getMailingListMeta();
        if ($target_list->getCampaignId()) {
          $this->campaign = $target_list->getCampaign();

          if (!$this->userIsAdmin() && $this->campaign->getStatus() == CampaignTable::STATUS_DELETED) {
            return $this->notFound();
          }

          $this->target_list = $target_list;
          $this->form = new MailingListForm($target_list);
        }
      }
    }

    $this->petition = $petition;
    $this->target_form = new EditPetitionTargetForm($petition, array(EditPetitionTargetForm::USER => $this->getGuardUser()));
    $this->open_edit = $request->getGetParameter('e') ? true : false;

    $this->setTemplate('edit');
    $this->includeIframeTransport();
    $this->includeChosen();
  }

  public function executeEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    $this->csrf_token = UtilCSRF::gen('target_activate');

    if (isset($route_params['type']) && $route_params['type'] == 'new') { // CREATE
      $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
      /* @var $campaign Campaign */
      if (!$campaign)
        return $this->notFound();

      if (!$this->getGuardUser()->isCampaignMember($campaign))
        return $this->noAccess();

      $target_list = new MailingList();
      $target_list->setCampaign($campaign);
      $target_list->setStatus(MailingListTable::STATUS_DRAFT);
    }
    else {
      $target_list = $this->findTargetList();
      /* @var $target_list MailingList */

      if (!$target_list)
        return $this->notFound();

      if (!$this->getGuardUser()->isTargetListMember($target_list, true))
        return $this->noAccess();

      $this->metas = $target_list->getMailingListMeta();
    }

    if ($target_list->getCampaignId()) {
      $this->campaign = $target_list->getCampaign();

      if (!$this->userIsAdmin() && $this->campaign->getStatus() == CampaignTable::STATUS_DELETED)
        return $this->notFound();
    }
    $this->target_list = $target_list;
    $this->form = new MailingListForm($target_list);

    if ($request->isMethod('post')) {
      $form_data = $request->getPostParameter($this->form->getName());
      if ($form_data) {
        $this->form->bind($form_data);

        if ($this->form->isValid()) {
          $was_new = $target_list->isNew();
          $this->form->save();
          $target_list->refresh();
          if ($was_new) {
            if (!$this->getGuardUser()->isCampaignAdmin($target_list->getCampaign())) {
              $tr = new TargetListRights();
              $tr->setUser($this->getGuardUser());
              $tr->setMailingList($target_list);
              $tr->setActive(1);
              $tr->save();
            }
            return $this->ajax()->redirectRotue('target_edit', array('id' => $target_list->getId()))->render();
          } else
            return $this->ajax()
                ->replaceWithPartial('#form', 'form', array('form' => new MailingListForm($target_list)))
                ->alert('Name updated', '', '#form', 'after')->render();
        } else {
          return $this->ajax()->form($this->form)->render();
        }
      }
    }

    $this->includeIframeTransport();
    $this->includeChosen();
  }

  public function executeContactPager(sfWebRequest $request) {
    $target_list = $this->findTargetList();
    /* @var $target_list MailingList */

    if (!$target_list)
      return $this->notFound();

    if (!$this->getGuardUser()->isTargetListMember($target_list, true))
      return $this->noAccess();

    $page = $request->getParameter('page');

    return $this->ajax()->replaceWithComponent('#contacts', 'target', 'contacts', array('target_list' => $target_list, 'page' => $page, 'no_filter' => true))->render();
  }

  public function executeMeta(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();

    if (isset($route_params['type'])) { // CREATE
      $target_list = $this->findTargetList();
      /* @var $target_list MailingList */

      if (!$target_list)
        return $this->notFound();

      if (!$this->getGuardUser()->isTargetListMember($target_list, true))
        return $this->noAccess();

      $meta = new MailingListMeta();
      $meta->setMailingList($target_list);

      switch ($route_params['type']) {
        case 'free':
          $meta->setKind(MailingListMeta::KIND_FREE);
          $route = 'target_meta_free';
          break;
        case 'choice':
          $meta->setKind(MailingListMeta::KIND_CHOICE);
          $route = 'target_meta_choice';
          break;
        case 'mapping':
          $meta->setKind(MailingListMeta::KIND_MAPPING);
          $route = 'target_meta_mapping';
          break;
        default:
          return $this->notFound();
      }

      $route_params = array('id' => $target_list->getId());
    } else { // EDIT
      $meta = MailingListMetaTable::getInstance()->find($request->getParameter('id'));
      /* @var $meta MailingListMeta */
      if (!$meta)
        return $this->notFound();

      $target_list = $meta->getMailingList();
      if (!$target_list)
        return $this->notFound();

      if (!$this->getGuardUser()->isTargetListMember($target_list, true))
        return $this->noAccess();

      $route = 'target_meta';
      $route_params = array('id' => $meta->getId());
    }

    $form = new MailingListMetaForm($meta);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if ($form->isValid()) {
        $form->save();
        $target_list->state(Doctrine_Record::STATE_DIRTY); // to invalidate cacheu
        $target_list->save();
        return $this->ajax()->replaceWithPartial('#metas', 'metas', array('metas' => $target_list->getMailingListMeta()))->render();
      } else
        return $this->ajax()->form($form)->render();
    }

    if (!$meta->isNew())
      return $this->ajax()
          ->remove('#meta_form_' . $meta->getId())
          ->afterPartial('#meta_' . $meta->getId(), 'meta', array('form' => $form, 'route' => $route, 'route_params' => $route_params))->render();
    else
      return $this->ajax()
          ->remove('#meta_form_')
          ->appendPartial('#metas tbody', 'meta', array('form' => $form, 'route' => $route, 'route_params' => $route_params))->render();
  }

  public function executeMetaDelete(sfWebRequest $request) {
    $meta = MailingListMetaTable::getInstance()->find($request->getParameter('id'));
    /* @var $meta MailingListMeta */
    if (!$meta)
      return $this->notFound();

    $target_list = $meta->getMailingList();
    if (!$target_list)
      return $this->notFound();

    if (!$this->getGuardUser()->isTargetListMember($target_list, true))
      return $this->noAccess();

    $csrf_token = UtilCSRF::gen('delete_target_meta', $meta->getId(), $target_list->getId(), $this->getUser()->getUserId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#meta_delete_modal .modal-body')->render();

      $id = $meta->getId();
      $meta->delete();
      return $this->ajax()->remove('#meta_' . $id)->modal('#meta_delete_modal', 'hide')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete_meta', array('id' => $meta->getId(), 'name' => $meta->getName(), 'csrf_token' => $csrf_token))
        ->modal('#meta_delete_modal')
        ->render();
  }

  public function executeContact(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();

    if (isset($route_params['type']) && $route_params['type'] == 'new') { // CREATE
      $target_list = $this->findTargetList();
      /* @var $target_list MailingList */

      if (!$target_list)
        return $this->notFound();

      if (!$this->getGuardUser()->isTargetListMember($target_list, true))
        return $this->noAccess();

      $contact = new Contact();
      $contact->setMailingList($target_list);

      $route = 'target_contact_new';
      $route_params = array('id' => $target_list->getId());
    } else { // EDIT
      $contact = ContactTable::getInstance()->find($request->getParameter('id'));
      /* @var $contact Contact */

      if (!$contact)
        return $this->notFound();

      $target_list = $contact->getMailingList();

      if (!$this->getGuardUser()->isTargetListMember($target_list, true))
        return $this->noAccess();

      $route = 'target_contact';
      $route_params = array('id' => $contact->getId(), 'page' => $request->getParameter('page'));
    }

    $form = new ContactForm($contact);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if ($form->isValid()) {
        $form->save();
        $target_list->state(Doctrine_Record::STATE_DIRTY); // to invalidate cache
        $target_list->save();
        $page = $request->getParameter('page');
        if ($page)
          return $this->ajax()->replaceWithComponent('#contacts', 'target', 'contacts', array('target_list' => $target_list, 'page' => $page, 'no_filter' => true))->render();
        else
          return $this->ajax()->replaceWithComponent('#contacts', 'target', 'contacts', array('target_list' => $target_list, 'last_page' => true, 'no_filter' => true))->render();
      } else
        return $this->ajax()->form($form)->render();
    }

    if (!$contact->isNew())
      return $this->ajax()
          ->remove('#contact_form_' . $contact->getId())
          ->afterPartial('#contact_' . $contact->getId(), 'contact', array('form' => $form, 'route' => $route, 'route_params' => $route_params))
          ->chosen('#contact_form_' . $contact->getId() . ' select', array('allow_single_deselect' => true))
          ->render();
    else
      return $this->ajax()
          ->remove('#contact_form_')
          ->afterPartial('#contacts tbody', 'contact', array('form' => $form, 'route' => $route, 'route_params' => $route_params))
          ->chosen('#contact_form_ select', array('allow_single_deselect' => true))
          ->render();
  }

  public function executeJoin(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#target_list table', 'after');

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('target_join'))
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();

    $id = $request->getPostParameter('id');
    if (!is_numeric($id))
      return $this->ajax()->alert('invalid data', 'Error')->render();

    $target_list = $this->findTargetList($id);
    /* @var $target_list MailingList */
    if (!$target_list)
      return $this->ajax()->alert('Target-list not found', 'Error')->render();

    $tr = $this->getGuardUser()->getTargetListRights($target_list);

    if ($tr && $tr->getActive())
      return $this->ajax()->alert('You are already Target-list member', '')->render();

    if ($this->getGuardUser()->isCampaignAdmin($target_list->getCampaignId())) {
      if (!$tr) {
        $tr = new TargetListRights();
        $tr->setUserId($this->getGuardUser()->getId());
        $tr->setMailingListId($target_list->getId());
      }
      $tr->setActive(1);
      $tr->save();

      return $this->ajax()->alert('Directly joined because you are Campaign-Admin', '')->render();
    }

    $ticket = TicketTable::getInstance()->generate(array(
        TicketTable::CREATE_AUTO_FROM => true,
        TicketTable::CREATE_TARGET_LIST => $target_list,
        TicketTable::CREATE_KIND => TicketTable::KIND_TARGET_LIST_MEMBER,
        TicketTable::CREATE_CHECK_DUPLICATE => true
    ));
    if ($ticket) {
      $ticket->save();
      $ticket->notifyAdmin();
    } else
      return $this->ajax()->alert('Application already pending', '')->render();

    return $this->ajax()->alert('Application has been sent to Campaign admin', '')->render();
  }

  public function executeEditMembers(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#target_list_members', 'after');

    $target_list = $this->findTargetList();
    /* @var $target_list MailingList */
    if (!$target_list)
      return $this->ajax()->alert('Target-list not found', 'Error')->render();

    if ($target_list->getCampaignId()) {
      if (!$this->userIsAdmin() && $target_list->getCampaign()->getStatus() == CampaignTable::STATUS_DELETED)
        return $this->notFound();
    }

    if (!$this->getGuardUser()->isCampaignAdmin($target_list->getCampaign()))
      return $this->ajax()->alert('You are not admin', 'Error')->render();

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('target_list_members'))
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();

    $ids = $request->getPostParameter('ids');
    $method = $request->getPostParameter('method');
    if (!in_array($method, array('enable', 'disable')))
      return $this->ajax()->alert('Something is wrong.', 'Error')->render();
    if (is_array($ids)) {
      foreach (TargetListRightsTable::getInstance()->queryByTargetListAndUsers($target_list, $ids)->execute() as $target_list_rights) {
        /* @var $target_list_rights TargetListRights */

        if ($method === 'enable')
          $target_list_rights->setActive(1);
        elseif ($method === 'disable')
          $target_list_rights->setActive(0);

        $target_list_rights->save();
      }
    }

    return $this->ajax()->replaceWithComponent('#target_list_members', 'target', 'members', array('target_list' => $target_list))->render();
  }

  public function executeActivate(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#form .form-actions', 'before');

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('target_activate'))
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();

    $id = $request->getPostParameter('id');
    if (!is_numeric($id))
      return $this->ajax()->alert('invalid data', 'Error')->render();

    $target_list = $this->findTargetList($id);
    /* @var $target_list MailingList */
    if (!$target_list)
      return $this->ajax()->alert('Target-list not found', 'Error')->render();

    if ($target_list->getStatus() == MailingListTable::STATUS_ACTIVE)
      return $this->ajax()->alert('Target-list is already active.', 'Error')->render();

    if ($this->getGuardUser()->isCampaignAdmin($target_list->getCampaignId())) {
      $target_list->setStatus(MailingListTable::STATUS_ACTIVE);
      $target_list->save();

      $petition_id = $request->getPostParameter('petition_id');
      if (is_numeric($petition_id) && $petition_id) {
        return $this->ajax()->redirectRotue('petition_target', array('id' => $petition_id))->render();
      } else {
        return $this->ajax()->redirectRotue('target_edit', array('id' => $target_list->getId()))->render();
      }
    }

    if (!$this->getGuardUser()->isTargetListMember($target_list))
      return $this->noAccess();

    $ticket = TicketTable::getInstance()->generate(array(
        TicketTable::CREATE_AUTO_FROM => true,
        TicketTable::CREATE_TARGET_LIST => $target_list,
        TicketTable::CREATE_KIND => TicketTable::KIND_TARGET_LIST_ACTIVATE,
        TicketTable::CREATE_CHECK_DUPLICATE => true
    ));
    if ($ticket) {
      $ticket->save();
      $ticket->notifyAdmin();
    } else
      return $this->ajax()->alert('Application already pending', '')->render();

    return $this->ajax()->alert('Application has been sent to Campaign admin', '')->render();
  }

  public function executeDeactivate(sfWebRequest $request) {
    if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN))
      return $this->noAccess();

    $this->ajax()->setAlertTarget('#form .form-actions', 'before');

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('target_activate'))
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();

    $id = $request->getPostParameter('id');
    if (!is_numeric($id))
      return $this->ajax()->alert('invalid data', 'Error')->render();

    $target_list = $this->findTargetList($id);
    /* @var $target_list MailingList */
    if (!$target_list)
      return $this->ajax()->alert('Target-list not found', 'Error')->render();

    $target_list->setStatus(MailingListTable::STATUS_DRAFT);
    $target_list->save();

    return $this->ajax()->redirectRotue('target_edit', array('id' => $target_list->getId()))->render();
  }

  public function executeDelete(sfWebRequest $request) {
    if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN))
      return $this->noAccess();

    $this->ajax()->setAlertTarget('#form .form-actions', 'before');

    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('target_activate'))
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();

    $id = $request->getPostParameter('id');
    if (!is_numeric($id))
      return $this->ajax()->alert('invalid data', 'Error')->render();

    $target_list = $this->findTargetList($id);
    /* @var $target_list MailingList */
    if (!$target_list)
      return $this->ajax()->alert('Target-list not found', 'Error')->render();

    if ($target_list->countActions())
      return $this->ajax()->alert('You can not delete Target-lists with connected Actions', 'Error')->render();

    $target_list->setStatus(MailingListTable::STATUS_DELETED);
    $target_list->save();

    return $this->ajax()->redirectRotue('target_edit', array('id' => $target_list->getId()))->render();
  }

  public function executeUpload(sfWebRequest $request) {
    $target_list = $this->findTargetList();
    /* @var $target_list MailingList */

    if (!$target_list)
      return $this->notFound();

    if (!$this->getGuardUser()->isTargetListMember($target_list, true))
      return $this->noAccess();

    $form1 = new ContactUploadStep1Form();
    $form2 = new ContactUploadStep2Form(array(), array('MailingList' => $target_list));

    if ($request->isMethod('post')) {
      if ($request->hasParameter($form1->getName())) {
        $form1->bind($request->getPostParameter($form1->getName()), $request->getFiles($form1->getName()));
        if ($form1->isValid()) {
          $filename = $form1->save();
          $form2->setSeparator($form1->getSeparator());
          $form2->setFile($filename, true);
          return $this->ajax()->replaceWithPartial('#upload_form', 'upload2', array('form' => $form2, 'target_list' => $target_list))->render(true);
        } else {
          return $this->ajax()->form($form1)->render(true);
        }
      } else {
        $form2_params = $request->getPostParameter($form2->getName());
        $bind_ok = $form2->bind($form2_params);
        if (!$bind_ok) {
          return $this->ajax()->alert('Critical error', '', '#upload_form .form-actions', 'before')->render();
        }
        if ($form2->isValid()) {
          if ($form2->save()) {
            $target_list->state(Doctrine_Record::STATE_DIRTY); // to invalidate cache
            $target_list->save();
            return $this->ajax()
                ->replaceWith('#upload_form', '<div id="upload_form"></div>')
                ->alert('Upload successfull', '', '#upload_form', 'append')
                ->replaceWithComponent('#contacts', 'target', 'contacts', array('target_list' => $target_list, 'page' => 1, 'no_filter' => true))
                ->render();
          } else {
            return $this->ajax()->form($form2)->alert('Upload Error', '', '#upload_form .form-actions', 'before')->render();
          }
        } else {
          return $this->ajax()->form($form2)->render();
        }
      }
    }

    return $this->ajax()->replaceWithPartial('#upload_form', 'upload1', array('form' => $form1, 'target_list' => $target_list))->render();
  }

  public function executeCopy(sfWebRequest $request) {
    $target_list = $this->findTargetList();
    /* @var $target_list MailingList */

    if (!$target_list)
      return $this->notFound();

    if (!$this->getGuardUser()->isTargetListMember($target_list, true))
      return $this->noAccess();

    if ($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN))
      $user = null;
    else
      $user = $this->getGuardUser();

    $form = new TargetListCopyForm(
      array('new_name' => 'Copy of ' . $target_list->getName()), array('user' => $user)
    );

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $data = $form->getValues();
        $campaign = $data['target'] ? CampaignTable::getInstance()->findById($data['target'], $this->userIsAdmin()) : null;
        $new = MailingListTable::getInstance()->copy($target_list, $campaign, $data['new_name']);

        if ($new) {
          $tr = new TargetListRights();
          $tr->setMailingListId($new->getId());
          $tr->setUserId($this->getGuardUser()->getId());
          $tr->setActive(1);
          $tr->save();

          return $this->ajax()
              ->modal('#target_copy_modal', 'hide')
              ->remove('#target_copy_modal')
              ->alert('Target-list copied', '', '#target_list', 'before', false, 'success')
              ->render();
        } else {
          return $this->ajax()->alert('Error on copy', '', '#target_list', 'before', false, 'success')->render();
        }
      } else
        return $this->ajax()->form($form)->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'copy', array('id' => $target_list->getId(), 'form' => $form))
        ->modal('#target_copy_modal')
        ->render();
  }

  public function executeCopyGlobal(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */

    if (!$campaign)
      return $this->notFound();

    if (!$this->getGuardUser()->isCampaignMember($campaign))
      return $this->noAccess();

    $form = new TargetListCopyGlobalForm();

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $data = $form->getValues();
        $target_list = $this->findTargetList($data['global']);
        $new = MailingListTable::getInstance()->copy($target_list, $campaign, $data['new_name']);

        if ($new) {
          $tr = new TargetListRights();
          $tr->setMailingListId($new->getId());
          $tr->setUserId($this->getGuardUser()->getId());
          $tr->setActive(1);
          $tr->save();

          return $this->ajax()
              ->modal('#target_copy_global_modal', 'hide')
              ->remove('#target_copy_gloabl_modal')
              ->alert('Target-list copied', '', '#target_list', 'before', false, 'success')
              ->render();
        } else {
          return $this->ajax()->alert('Error on copy', '', '#target_list', 'before', false, 'success')->render();
        }
      } else
        return $this->ajax()->form($form)->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'copy_global', array('id' => $campaign->getId(), 'form' => $form))
        ->modal('#target_copy_global_modal')
        ->render();
  }

  public function executeContactDelete(sfWebRequest $request) {
    $contact = ContactTable::getInstance()->find($request->getParameter('id'));
    /* @var $contact Contact */

    if (!$contact)
      return $this->notFound();

    $target_list = $contact->getMailingList();

    if (!$this->getGuardUser()->isTargetListMember($target_list, true))
      return $this->noAccess();

    $csrf_token = UtilCSRF::gen('delete_contact', $contact->getId(), $this->getUser()->getUserId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#contact_delete_modal .modal-body')->render();

      $id = $contact->getId();
      $contact->delete();
      return $this->ajax()->remove('#contact_' . $id)->modal('#contact_delete_modal', 'hide')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $contact->getId(), 'name' => $contact->getFullname(), 'csrf_token' => $csrf_token))
        ->modal('#contact_delete_modal')
        ->render();
  }

  public function executeTruncate(sfWebRequest $request) {
    $target_list = $this->findTargetList();
    /* @var $target_list MailingList */

    if (!$target_list)
      return $this->notFound();

    if (!$this->getGuardUser()->isTargetListMember($target_list, true))
      return $this->noAccess();

    $csrf_token = UtilCSRF::gen('truncate_target_list', $target_list->getId(), $this->getUser()->getUserId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#contact_truncate_modal .modal-body')->render();

      $id = $target_list->getId();
      $target_list->getContact()->delete();
      return $this->ajax()->remove('#contacts table tbody tr')->modal('#contact_truncate_modal', 'hide')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'truncate', array('id' => $target_list->getId(), 'name' => $target_list->getName(), 'csrf_token' => $csrf_token))
        ->modal('#contact_truncate_modal')
        ->render();
  }

}
