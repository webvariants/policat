<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
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

      $pager_query_params = array();
      if (isset($this->campaign_id) && $this->campaign_id) {
        $query->andWhere($query->getRootAlias() . '.campaign_id = ?', $this->campaign_id);
        $pager_query_params['campaign_id'] = $this->campaign_id;
      } else {
        $this->campaign_id = null;
      }
      if (isset($this->petition_id) && $this->petition_id) {
        $query->andWhere($query->getRootAlias() . '.petition_id = ?', $this->petition_id);
        $pager_query_params['petition_id'] = $this->petition_id;
      } else {
        $this->petition_id = null;
      }

      $this->tickets = new policatPager($query, $page, 'ticket_todo', array(), true, 3, null, null, $pager_query_params);
      $this->csrf_token = UtilCSRF::gen('tickets');
    }
  }

  function executeTicket() {
    $ticket = $this->ticket;
    /* @var $ticket Ticket */

    $this->csrf_token = in_array($ticket->getStatus(), array(TicketTable::STATUS_APPROVED, TicketTable::STATUS_DENIED)) ? null : UtilCSRF::gen('tickets_' . $ticket->getId());

    $routing = $this->getContext()->getRouting();

    $this->is_notice = TicketTable::$KIND_HANDLER[$ticket->getKind()] === 'none' && TicketTable::$KIND_HANDLER_DENY[$ticket->getKind()] === 'none';

    $this->getContext()->getConfiguration()->loadHelpers(array('Date'));

    $subst = array(
        '#FROM#' => $ticket->getFromId() ? sprintf('<a href="mailto:%s">%s (%s)</a>', Util::enc($ticket->getFrom()->getEmailAddress()), Util::enc($ticket->getFrom()->getFullName()), Util::enc($ticket->getFrom()->getOrganisation())) : '',
        '#TO#' => $ticket->getToId() ? Util::enc($ticket->getTo()->getFullName()) : '',
        '#CAMPAIGN#' => $ticket->getCampaignId() ? Util::enc($ticket->getCampaign()->getName()) : '',
        '#PETITION#' => $ticket->getPetitionId() ? sprintf('<a href="%s">%s</a>', $routing->generate('petition_overview', array('id' => $ticket->getPetitionId())), $ticket->getPetition()->getName()) : '',
        '#WIDGET#' => $ticket->getWidgetId() ? sprintf('<a href="%s">%s</a>', $routing->generate('widget_edit', array('id' => $ticket->getWidgetId())), $ticket->getWidgetId()) : '',
        '#TARGETLIST#' => $ticket->getTargetListId() ? sprintf('<a href="%s">%s</a>', $routing->generate('target_edit', array('id' => $ticket->getTargetListId())), Util::enc($ticket->getTargetList()->getName())): '',
        '#TEXT#' => $ticket->getText() ? sprintf('<blockquote style="white-space:pre-line;margin:0">%s</blockquote>', Util::enc($ticket->getText())) : '',
        '#DATE#' => format_date($ticket->getCreatedAt(), 'yyyy-MM-dd HH:mm'),
        '#BUY-PACKAGE#' => $this->packageLink($ticket)
    );

    $template = TicketTable::$KIND_TEMPLATE[$ticket->getKind()];
    $missing = array();
    foreach ($subst as $key => $value) {
      if ($value && strpos($template, $key) === false && $key !== '#TO#' && $key !== '#BUY-PACKAGE#') {
        $missing[] = $value;
      }
    }

    $this->text = strtr($template, $subst) . ($missing ? '<br />' . implode(', ', $missing) : '');
  }

  private function packageLink(Ticket $ticket) {
    if (!$ticket->getCampaignId()) {
      return '';
    }

    $campaign = $ticket->getCampaign();

    if (!$campaign->getBillingEnabled() && !$campaign->getQuotaId()) {
      return '';
    }

    if (!$campaign->getOrderId()) {
      return sprintf('<a class="btn btn-mini" href="%s">Buy package</a>', $this->getContext()->getRouting()->generate('order_new', array('id' => $campaign->getId())));
    }

    if ($campaign->getOrderId() && $campaign->getOrder()->getUserId() == $this->getGuardUser()->getId()) {
      return sprintf('<a class="btn btn-mini" href="%s">Show active order</a>', $this->getContext()->getRouting()->generate('order_show', array('id' => $campaign->getOrderId())));
    }

    return '';
  }

}
