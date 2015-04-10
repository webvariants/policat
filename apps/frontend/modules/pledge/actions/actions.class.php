<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * pledge actions.
 *
 * @package    policat
 * @subpackage pledge
 * @author     Martin
 */
class pledgeActions extends policatActions {

  public function executeList(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition)
      return $this->notFound();

    if ($petition->getKind() != Petition::KIND_PLEDGE)
      return $this->noAccess();

    if (!$petition->isEditableBy($this->getGuardUser()))
      return $this->noAccess();

    $this->petition = $petition;

    $this->pledge_items = $petition->getPledgeItems();
  }

  public function executeNew(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition)
      return $this->notFound();

    if (!$petition->isEditableBy($this->getGuardUser()))
      return $this->noAccess();

    if ($petition->getKind() != Petition::KIND_PLEDGE)
      return $this->noAccess();

    $pledge_item = new PledgeItem();
    $pledge_item->setPetition($petition);

    $form = new PledgeItemForm($pledge_item);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $form->save();
        $petition->state(Doctrine_Record::STATE_DIRTY); // trigger widget update
        $petition->save();

        return $this->ajax()->remove('#pledge_item_form_')
            ->appendPartial('#pledge_items tbody', 'item_row', array('pledge_item' => $pledge_item))
            ->render();
      }

      return $this->ajax()->form($form)->render();
    }

    return $this->ajax()
        ->remove('#pledge_item_form_')
        ->appendPartial('#pledge_items tbody', 'item_form', array(
            'form' => $form,
            'pledge_item' => $pledge_item,
            'route' => 'pledge_new',
            'route_params' => array('id' => $petition->getId())
        ))
        ->select2color('#pledge_item_form_ select.select2-color')
        ->render();
  }

  public function executeEdit(sfWebRequest $request) {
    $pledge_item = PledgeItemTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $pledge_item PledgeItem */
    if (!$pledge_item)
      return $this->notFound();

    $petition = $pledge_item->getPetition();

    if (!$petition->isEditableBy($this->getGuardUser()))
      return $this->noAccess();

    if ($petition->getKind() != Petition::KIND_PLEDGE)
      return $this->noAccess();

    $form = new PledgeItemForm($pledge_item);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $form->save();
        $petition->state(Doctrine_Record::STATE_DIRTY); // trigger widget update
        $petition->save();

        return $this->ajax()
            ->remove('#pledge_item_form_' . $pledge_item->getId())
            ->replaceWithPartial('#pledge_item_' . $pledge_item->getId(), 'item_row', array('pledge_item' => $pledge_item))
            ->render();
      }

      return $this->ajax()->form($form)->render();
    }

    return $this->ajax()
        ->remove('#pledge_item_form_' . $pledge_item->getId())
        ->afterPartial('#pledge_item_' . $pledge_item->getId(), 'item_form', array(
            'form' => $form,
            'pledge_item' => $pledge_item,
            'route' => 'pledge_edit',
            'route_params' => array('id' => $pledge_item->getId())
        ))
        ->select2color('#pledge_item_form_' . $pledge_item->getId() . ' select.select2-color')
        ->render();
  }

  public function executeStats(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition)
      return $this->notFound();

    if (!$petition->isEditableBy($this->getGuardUser()))
      return $this->noAccess();

    if ($petition->getKind() != Petition::KIND_PLEDGE)
      return $this->noAccess();

    if ($petition->getMailingListId()) {
      $mailing_list = $petition->getMailingList();

      $filter_form = new FilterContactForm();
      $filter_form->bindSelf('p' . $petition->getId());

      $page = $request->getUrlParameter('page');
      if ($page < 1) {
        $page = 1;
      }

      $contact_table = ContactTable::getInstance();
      $contacts = new policatPager($filter_form->filter($contact_table->queryByTargetList($mailing_list, $petition)), $page, 'pledge_stats_pager', array('id' => $petition->getId()), true, 20);

      $active_pledge_item_ids = $petition->getActivePledgeItemIds();
      $pledges = PledgeTable::getInstance()->getPledgesForContacts($contacts->getResults(), $active_pledge_item_ids);
      $pledge_items = PledgeItemTable::getInstance()->fetchByIds($active_pledge_item_ids);

      if ($request->getUrlParameter('page')) {
        return $this->ajax()->replaceWithPartial('#contacts', 'contacts', array(
                'contacts' => $contacts,
                'petition_id' => $petition->getId(),
                'active_pledge_item_ids' => $active_pledge_item_ids,
                'pledges' => $pledges,
                'pledge_items' => $pledge_items
            ))
            ->tooltip('#contacts .add_tooltip')
            ->render();
      }

      $this->form = $filter_form;
      $this->petition = $petition;
      $this->contacts = $contacts;
      $this->no_target_list = false;
      $this->active_pledge_item_ids = $active_pledge_item_ids;
      $this->pledges = $pledges;
      $this->pledge_items = $pledge_items;
    } else {
      $this->no_target_list = true;
    }
  }

  public function executeContactEdit(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('petition_id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition)
      return $this->notFound();

    if (!$petition->isEditableBy($this->getGuardUser()))
      return $this->noAccess();

    if ($petition->getKind() != Petition::KIND_PLEDGE)
      return $this->noAccess();

    $petition_contact = PetitionContactTable::getInstance()->findOneByPetitionIdAndContactId($petition->getId(), $request->getParameter('id'));

    if (!$petition_contact) {
      $contact = ContactTable::getInstance()->find($request->getParameter('id'));
      /* @var $contact Contact */

      if (!$contact)
        return $this->notFound();

      if ($contact->getMailingListId() != $petition->getMailingListId()) {
        return $this->notFound();
      }

      $petition_contact = new PetitionContact();
      $petition_contact->setPetition($petition);
      $petition_contact->setContact($contact);
    } else {
      $contact = $petition_contact->getContact();
    }

    $form = new PetitionContactForm($petition_contact);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if ($form->isValid()) {
        $form->save();
        $active_pledge_item_ids = $petition->getActivePledgeItemIds();
        $pledges = PledgeTable::getInstance()->getPledgesForContacts(array($contact), $active_pledge_item_ids);
        $pledge_items = PledgeItemTable::getInstance()->fetchByIds($active_pledge_item_ids);
        return $this->ajax()
            ->remove('#contact_edit_row_' . $contact->getId())
            ->replaceWithPartial('#contact_' . $contact->getId(), 'contact', array(
                'contact' => $contact,
                'active_pledge_item_ids' => $active_pledge_item_ids,
                'pledges' => $pledges,
                'pledge_items' => $pledge_items,
                'petition_id' => $petition->getId()
            ))
            ->tooltip('#contact_' . $contact->getId() . ' .add_tooltip')
            ->render();
      } else {
        return $this->ajax()->form($form)->render();
      }
    }

    return $this->ajax()
        ->remove('#contact_edit_row_' . $contact->getId())
        ->afterPartial('#contact_' . $contact->getId(), 'contactEdit', array('form' => $form))
        ->render();
  }

  public function executeDownload(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition)
      return $this->notFound();

    if (!$petition->isEditableBy($this->getGuardUser()))
      return $this->noAccess();

    if ($petition->getKind() != Petition::KIND_PLEDGE)
      return $this->noAccess();

    $target_list = $petition->getMailingList();
    if (!$target_list) {
      return $this->notFound();
    }

    $contact_data = ContactTable::getInstance()->queryFullData($target_list, $petition)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    $pledge_data = ContactTable::getInstance()->queryByMailingList($target_list, $petition)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    $pledge_data_contacts = PetitionContactTable::getInstance()->queryByPetition($petition->getId())->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    $mailing_list_meta = $target_list->getMailingListMeta();
    $pledge_items = $petition->getPledgeItems();
    $head = array('email', 'gender', 'first name', 'last name', 'country', 'language');
    foreach ($mailing_list_meta as $mlm) {
      /* @var $mlm MailingListMeta */

      if ($mlm->getKind() != MailingListMeta::KIND_MAPPING) {
        $head[] = $mlm->getName();
      }
    }

    foreach ($pledge_items as $pledge_item) {
      /* @var $pledge_item PledgeItem */

      $head[] = $pledge_item->getName();
      $head[] = 'pledge date';
    }

    $head[] = 'comment';

    $out = fopen('php://temp/export', 'a+');

    foreach ($contact_data as $id => $contact) {
      $pledges = array_key_exists($id, $pledge_data) ? $pledge_data[$id]['Pledges'] : array();

      $data = array(
          $contact['email'],
          array_key_exists($contact['gender'], Contact::$GENDER_SHOW) ? Contact::$GENDER_SHOW[$contact['gender']] : null,
          $contact['firstname'],
          $contact['lastname'],
          $contact['country'],
          $contact['language_id']
      );

      $contact_metas = array();
      foreach ($contact['ContactMeta'] as $cm) {
        $cm_id = $cm['mailing_list_meta_id'];
        if (!array_key_exists($cm_id, $contact_metas)) {
          $contact_metas[$cm_id] = array();
        }

        if ($cm['MailingListMetaChoice']) {
          $contact_metas[$cm_id][] = $cm['MailingListMetaChoice']['choice'];
        } else {
          $contact_metas[$cm_id][] = $cm['value'];
        }
      }

      foreach ($mailing_list_meta as $mlm) {
        /* @var $mlm MailingListMeta */

        if ($mlm->getKind() != MailingListMeta::KIND_MAPPING) {
          $data[] = array_key_exists($mlm->getId(), $contact_metas) ? implode('|', $contact_metas[$mlm->getId()]) : '';
        }
      }

      foreach ($pledge_items as $pledge_item) {
        /* @var $pledge_item PledgeItem */

        $pledge_status = array_key_exists($pledge_item->getId(), $pledges) ? $pledges[$pledge_item->getId()]['status'] : null;
        $data[] = array_key_exists($pledge_status, PledgeTable::$STATUS_SHOW) ? PledgeTable::$STATUS_SHOW[$pledge_status] : null;
        $data[] = array_key_exists($pledge_item->getId(), $pledges) ? $pledges[$pledge_item->getId()]['status_at'] : null;
      }

      $data[] = array_key_exists($id, $pledge_data_contacts) ? $pledge_data_contacts[$id]['comment'] : null;

      fputcsv($out, $data, ';');
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/csv');
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename=' . '"pledges.csv"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    ob_clean();
    flush();

    $stdout = fopen('php://output', 'w');
    fwrite($stdout, "\xEF\xBB\xBF");
    fputcsv($stdout, $head, ';');
    fclose($stdout);

    rewind($out);
    fpassthru($out);
    fclose($out);

    exit;
  }

}
