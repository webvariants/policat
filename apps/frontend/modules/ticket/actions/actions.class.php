<?php

class ticketActions extends policatActions {

  protected function hasTicketRight(Ticket $ticket) {
    $allowed = false;
    if ($ticket->getToId())
      return $ticket->getToId() == $this->getGuardUser()->getId();
    if (!$allowed && $ticket->getPetitionId())
      $allowed = $this->getGuardUser()->isPetitionAdmin($ticket->getPetitionId());
    if (!$allowed && $ticket->getCampaignId())
      $allowed = $this->getGuardUser()->isCampaignAdmin($ticket->getCampaignId());
    if (!$allowed)
      $allowed = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
    return $allowed;
  }

  public function executeAction(sfWebRequest $request) {
    if ($request->getPostParameter('csrf_token') !== UtilCSRF::gen('tickets'))
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#todo', 'append')->render();

    $ids = $request->getPostParameter('ids');
    $method = $request->getPostParameter('method');
    if (!in_array($method, array('approve', 'decline')))
      return $this->ajax()->alert('Something is wrong.', 'Error', '#todo')->render();
    if (is_array($ids)) {
      $tickets = TicketTable::getInstance()->queryIds($ids)->execute();
      foreach ($tickets as $ticket) {
        /* @var $ticket Ticket */

        if (in_array($ticket->getStatus(), array(TicketTable::STATUS_APPROVED, TicketTable::STATUS_DENIED)))
          continue;

        if (!$this->hasTicketRight($ticket))
          return $this->ajax()->alert('You have no rights to handle this ticket.', 'Error', '#todo', 'append')->render();

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
        }
        $ticket->save();
      }
    }

    $vars = array();
    $campaign_id = $request->getPostParameter('campaign_id');
    if (is_numeric($campaign_id))
      $vars['campaign_id'] = $campaign_id;
    $petition_id = $request->getPostParameter('petition_id');
    if (is_numeric($petition_id))
      $vars['petition_id'] = $petition_id;

    if ($request->getPostParameter('view') == 'close')
      $this->ajax()->modal('#ticket_view_modal', 'hide')->remove('#ticket_view_modal');

    return $this->ajax()->replaceWithComponent('#todo', 'ticket', 'todo', $vars)->render();
  }

  public function executeTodo(sfWebRequest $request) {
    $page = $request->getParameter('page', 1);
    return $this->ajax()->replaceWithComponent('#todo', 'ticket', 'todo', array('page' => $page))->render();
  }

  public function executeView(sfWebRequest $request) {
    $ticket = TicketTable::getInstance()->find($request->getParameter('id'));

    if (!$ticket)
      return $this->notFound();

    if (!$this->hasTicketRight($ticket))
      return $this->ajax()->alert('You have no rights to handle this ticket.', 'Error', '#todo', 'append')->render();

    $csrf_token = in_array($ticket->getStatus(), array(TicketTable::STATUS_APPROVED, TicketTable::STATUS_DENIED)) ? null : UtilCSRF::gen('tickets');

    return $this->ajax()
        ->appendPartial('body', 'view', array(
            'ticket' => $ticket,
            'csrf_token' => $csrf_token,
            'campaign_id' => $request->getGetParameter('campaign_id'),
            'petition_id' => $request->getGetParameter('petition_id')
        ))
        ->modal('#ticket_view_modal')
        ->render();
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
        $pr->setAdmin(0);
        $pr->save();
      }
      return;
    }

    $pr = new PetitionRights();
    $pr->setPetitionId($ticket->getPetitionId());
    $pr->setUserId($ticket->getFromId());
    $pr->setActive(1);
    $pr->setMember(1);
    $pr->setAdmin(0);
    $pr->save();
  }

  protected function joinPetitionAdmin(Ticket $ticket) {
    $pr = PetitionRightsTable::getInstance()->queryByPetitionAndUser($ticket->getPetition(), $ticket->getFrom())->fetchOne();
    if ($pr) {
      /* @var $pr PetitionRights */
      $pr->setAdmin(1);
      $pr->save();
    }
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
}