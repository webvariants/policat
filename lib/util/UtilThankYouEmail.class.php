<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilThankYouEmail {

  public static function send(PetitionSigning $signing) {
    if ($signing->getThankSent()) {
      return;
    }

    $petition = $signing->getPetition();

    if ($petition->getThankYouEmail() != Petition::THANK_YOU_EMAIL_YES) {
      return;
    }

    $widget = $signing->getWidget();
    $campaign = $petition->getCampaign();
    $petition_text = $widget->getPetitionText();

    $subject = trim($petition_text->getThankYouEmailSubject());
    $body = trim($petition_text->getThankYouEmailBody());

    if (!$subject || !$body) {
      return;
    }

    $delete = UtilLink::deleteSigning($signing->getId(), $signing->getDeleteCode());
    $url_ref_ = $signing->getField(Petition::FIELD_REF);
    $url_readmore_ = $petition->getReadMoreUrl();
    $url_ref = UtilPolicat::firstString(array($url_ref_, $url_readmore_));
    $url_readmore = UtilPolicat::firstString(array($url_readmore_, $url_ref_));
    $from = $petition->getFrom();
    $to = $signing->getEmail();
    $additional_subst = array(
        '#REFERER-URL#' => $url_ref,
        '#READMORE-URL#' => $url_readmore,
        '#DISCONFIRMATION-URL#' => $delete
    );

    $subst = array_merge($additional_subst, $widget->getDataOwnerSubst("\n", $petition));

    UtilMail::sendWithSubst(null, $from, $to, $subject, $body, $petition_text, $widget, $subst, $signing->getSubst(), true);

    QuotaTable::getInstance()->useQuota($campaign->getQuotaId(), 1);
    $signing->setQuotaThankYouId($campaign->getQuotaId());
    $signing->setThankSent(1);
  }

}
