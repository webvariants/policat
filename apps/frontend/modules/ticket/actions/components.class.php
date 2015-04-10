<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ticketComponents extends policatComponents {

  function executeTodo() {
    $page = isset($this->page) ? $this->page : 1;

    $user = $this->getGuardUser();
    if ($user) {
      $query = TicketTable::getInstance()->queryForUser($this->getGuardUser(), array(
          TicketTable::STATUS_NEW, TicketTable::STATUS_READ
        ));

      if (isset($this->campaign_id))
        $query->andWhere($query->getRootAlias() . '.campaign_id = ?', $this->campaign_id);
      if (isset($this->petition_id))
        $query->andWhere($query->getRootAlias() . '.petition_id = ?', $this->petition_id);

      $this->tickets = new policatPager($query, $page, 'ticket_todo', array(), true, 10);
      $this->csrf_token = UtilCSRF::gen('tickets');
    }
  }

}
