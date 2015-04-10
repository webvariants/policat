<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class targetComponents extends policatComponents {

  public function executeList() {
    if (isset($this->campaign))
      $this->target_lists = MailingListTable::getInstance()->queryByCampaign($this->campaign, false, $this->userIsAdmin())->execute();
    else
      $this->target_lists = MailingListTable::getInstance()->queryGlobal()->execute();

    $this->csrf_token_join = UtilCSRF::gen('target_join');
  }

  public function executeContacts() {
    $filter_form = new FilterContactForm();
    $filter_form->bindSelf('tc' . $this->target_list->getId());
    $this->form = $filter_form;

    $page = isset($this->page) ? $this->page : 1;
    $contact_table = ContactTable::getInstance();

    $this->contacts = new policatPager($filter_form->filter($contact_table->queryByTargetList($this->target_list)), $page, 'target_contact_pager', array('id' => $this->target_list->getId()), true, 20);
    if (isset($this->last_page) && $this->last_page)
      $this->contacts = new policatPager($filter_form->filter($contact_table->queryByTargetList($this->target_list)), $this->contacts->getLastPage(), 'target_contact_pager', array('id' => $this->target_list->getId()), true, 20);
  }

  public function executeMembers() {
    $this->target_list_rights_list = TargetListRightsTable::getInstance()->queryByTargetList($this->target_list)->execute();

    $this->admin = $this->getGuardUser()->isCampaignAdmin($this->target_list->getCampaign());
    $this->csrf_token = UtilCSRF::gen('target_list_members');
  }

}
