<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ticketActions extends policatActions {

  protected function hasTicketRight(Ticket $ticket) {
    $allowed = false;
    if ($ticket->getToId())
      return $ticket->getToId() == $this->getGuardUser()->getId();
    if (!$allowed && $ticket->getCampaignId())
      $allowed = $this->getGuardUser()->isCampaignAdmin($ticket->getCampaignId());
    if (!$allowed)
      $allowed = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
    return $allowed;
  }

  public function executeAction(sfWebRequest $request) {
    $id = $request->getPostParameter('id');
    $method = $request->getPostParameter('method');
    if (!in_array($method, array('approve', 'decline')))
      return $this->ajax()->alert('Something is wrong.', 'Error', '#todo')->render();
    if (is_numeric($id)) {
      $ticket = TicketTable::getInstance()->findOneById($id);
      /* @var $ticket Ticket */

      if (!$ticket) {
        return $this->ajax()->alert('Ticket not found', 'Error', '#todo', 'append')->render();
      }

      if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('tickets_' . $ticket->getId())) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#todo', 'append')->render();
      }

      if (in_array($ticket->getStatus(), array(TicketTable::STATUS_APPROVED, TicketTable::STATUS_DENIED))) {
        return $this->ajax()->alert('Ticket not open', 'Error', '#todo', 'append')->render();
      }

      if (!$this->hasTicketRight($ticket)) {
        return $this->ajax()->alert('You have no rights to handle this ticket.', 'Error', '#todo', 'append')->render();
      }

      if ($method === 'approve') {
        $ticket->setStatus(TicketTable::STATUS_APPROVED);
        $handler = $ticket->getKindHandler();
        if (method_exists($this, $handler)) {
          $this->$handler($ticket);
        } else {
          return $this->ajax()->alert('No handler for ticket.', 'Error', '#todo')->render();
        }
      } else {
        $ticket->setStatus(TicketTable::STATUS_DENIED);
        $deny_handler = $ticket->getKindDenyHandler();
        if (method_exists($this, $deny_handler)) {
          $this->$deny_handler($ticket);
        } else {
          return $this->ajax()->alert('No deny handler for ticket.', 'Error', '#todo')->render();
        }
      }
      $ticket->save();
    }

    $vars = array();
    $campaign_id = $request->getPostParameter('campaign_id');
    if (is_numeric($campaign_id)) {
      $vars['campaign_id'] = $campaign_id;
    }
    $petition_id = $request->getPostParameter('petition_id');
    if (is_numeric($petition_id)) {
      $vars['petition_id'] = $petition_id;
    }

    return $this->ajax()->click('#ticket_reload')->render();
  }

  public function executeTodo(sfWebRequest $request) {
    $page = $request->getParameter('page', 1);
    $campaign_id = $request->getGetParameter('campaign_id');
    $petition_id = $request->getGetParameter('petition_id');
    if (!is_numeric($campaign_id)) {
      $campaign_id = null;
    }
    if (!is_numeric($petition_id)) {
      $petition_id = null;
    }

    return $this->ajax()->replaceWithComponent('#todo', 'ticket', 'todo', array(
          'page' => $page,
          'campaign_id' => $campaign_id,
          'petition_id' => $petition_id
      ))->render();
  }

  protected function joinCampaign(Ticket $ticket) {
    $cr = CampaignRightsTable::getInstance()->queryByCampaignAndUser($ticket->getCampaign(), $ticket->getFrom())->fetchOne();
    if ($cr) {
      /* @var $cr CampaignRights */
      if (!$cr->getActive()) {
        $cr->setActive(1);
        $cr->setMember(1);
        $cr->setAdmin(0);
        $cr->save();
      }
      return;
    }

    $cr = new CampaignRights();
    $cr->setCampaignId($ticket->getCampaignId());
    $cr->setUserId($ticket->getFromId());
    $cr->setActive(1);
    $cr->setMember(1);
    $cr->setAdmin(0);
    $cr->save();
  }

  protected function joinPetition(Ticket $ticket) {
    $pr = PetitionRightsTable::getInstance()->queryByPetitionAndUser($ticket->getPetition(), $ticket->getFrom())->fetchOne();
    if ($pr) {
      /* @var $pr PetitionRights */
      if (!$pr->getActive()) {
        $pr->setActive(1);
        $pr->setMember(1);
        $pr->save();
      }
      return;
    }

    $pr = new PetitionRights();
    $pr->setPetitionId($ticket->getPetitionId());
    $pr->setUserId($ticket->getFromId());
    $pr->setActive(1);
    $pr->setMember(1);
    $pr->save();
  }

  protected function widgetDataOwner(Ticket $ticket) {
    $widget = $ticket->getWidget();
    if ($widget) {
      $widget->setDataOwner(WidgetTable::DATA_OWNER_YES);
      $widget->save();
    }
  }

  protected function targetListMember(Ticket $ticket) {
    $tr = TargetListRightsTable::getInstance()->queryByTargetListAndUser($ticket->getTargetList(), $ticket->getFrom())->fetchOne();
    if (!$tr) {
      $tr = new TargetListRights();
      $tr->setMailingList($ticket->getTargetList());
      $tr->setUser($ticket->getFrom());
    }
    /* @var $tr TargetListRights */
    $tr->setActive(1);
    $tr->save();
  }

  protected function targetListActivate(Ticket $ticket) {
    $ticket->getTargetList()->setStatus(MailingListTable::STATUS_ACTIVE);
    $ticket->getTargetList()->save();
  }

  protected function userUnblock(Ticket $ticket) {
    $user = $ticket->getFrom();
    if (!$user->hasPermission('user'))
      $user->addPermissionByName('user');
  }

  protected function privacyPolicyChanged(Ticket $ticket) {
    
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

  protected function resignDataOfficer(Ticket $ticket) {
    $campaign = $ticket->getCampaign();
    $campaign->setDataOwner($ticket->getTo());
    $campaign->save();

    $this->removeOldResignAndCallTickets($campaign);
  }

  protected function callDataOfficer(Ticket $ticket) {
    $campaign = $ticket->getCampaign();
    $campaign->setDataOwner($ticket->getFrom());
    $campaign->save();

    $this->removeOldResignAndCallTickets($campaign);
  }

  protected function widgetDeny(Ticket $ticket) {
    if ($ticket->getWidgetId()) {
      $widget = $ticket->getWidget();
      $widget->setStatus(Widget::STATUS_BLOCKED);
      $widget->save();
    }
  }

  protected function none() {
    
  }

}
