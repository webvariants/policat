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
 * Ticket
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Ticket extends BaseTicket {

  public function getKindName() {
    return TicketTable::$KIND_NAME[$this->getKind()];
  }

  public function getKindHandler() {
    return TicketTable::$KIND_HANDLER[$this->getKind()];
  }

  public function getKindDenyHandler() {
    return TicketTable::$KIND_HANDLER_DENY[$this->getKind()];
  }

  public function notifyAdmin($forceSubject = null, $replyTo = null) {
    $tos = array();

    if ($this->getToId()) {
      $email = $this->getTo()->getSwiftEmail();
      if ($email) {
        $tos[] = array($email, $this->getTo());
      }
    }

    if (!$tos && $this->getCampaignId()) {
      $crs = CampaignRightsTable::getInstance()->queryByCampaignAndAdmin($this->getCampaign())->execute();
      foreach ($crs as $cr) { /* @var $cr CampaignRights */
        $email = $cr->getUser()->getSwiftEmail();
        if ($email) {
          $tos[] = array($email, $cr->getUser()->getId());
        }
      }
    }

    if ($tos) {
      $subst_escape = array();

      $subject = $forceSubject ? $forceSubject : 'Ticket-Notification';
      $body = "A new ticket about the following subject has been created:\n\n";
      $body.= "   Topic: " . $this->getKindName() . "\n";
      if ($this->getCampaignId())
        $body.= "Campaign: " . Util::enc($this->getCampaign()->getName()) . "\n";
      if ($this->getPetitionId())
        $body.= "  Action: " . Util::enc($this->getPetition()->getName()) . "\n";
      if ($this->getWidgetId())
        $body.= "  Widget: " . $this->getWidgetId() . "\n";
      if ($this->getFromId())
        $body.= "    User: " . Util::enc($this->getFrom()->getFullName()) . "\n";

      if ($this->getText()) {
        $body .= "  Text:\n\n\n<blockquote><pre>#TEXT#</pre></blockquote>\n\n";
        $subst_escape['#TEXT#'] = $this->getText();
      }

      // [Click here to validate](#VALIDATION-URL#)
      $body .= "\n\n[Go to dashboard](" . sfContext::getInstance()->getRouting()->generate('dashboard', array(), true) . ")\n\n<div markdown=\"1\" style=\"height: 1px\"></div>";

      foreach ($tos as $to) {
        \UtilMail::send('Ticket', 'User-' . $to[1], null, $to[0], $subject, $body, null, null, $subst_escape, $replyTo, array(), true);
      }
    }
  }

}
