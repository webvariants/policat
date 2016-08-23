<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilEmailValidation {

  public static function send(PetitionSigning $signing, $subject_prefix = null) {
    if ($signing->getValidationKind() != PetitionSigning::VALIDATION_KIND_EMAIL) {
      return;
    }

    $widget = $signing->getWidget();
    $petition = $widget->getPetition();
    $petition_text = $widget->getPetitionText();
    $subject = $petition_text->getEmailValidationSubject();
    $body = $petition_text->getEmailValidationBody();
    $validation = UtilLink::signValidation($signing->getId(), $signing->getValidationData());
    if (!$signing->getDeleteCode()) { // migrate old signings on the fly
      $signing->setDeleteCode(PetitionSigning::genCode());
      $signing->save();
    }
    $delete = UtilLink::deleteSigning($signing->getId(), $signing->getDeleteCode());

    $url_ref_ = $signing->getField(Petition::FIELD_REF); // $this->getValue('ref');
    $url_readmore_ = $petition->getReadMoreUrl();
    $url_ref = UtilPolicat::firstString(array($url_ref_, $url_readmore_));
    $url_readmore = UtilPolicat::firstString(array($url_readmore_, $url_ref_));
    $from = $petition->getFrom();
    $to = $signing->getEmail();
    $additional_subst = array(
        'URL-REFERER' => $url_ref, // deprecated
        'URL-READMORE' => $url_readmore, // deprecated
        'VALIDATION' => $validation, // deprecated
        '#REFERER-URL#' => $url_ref,
        '#READMORE-URL#' => $url_readmore,
        '#VALIDATION-URL#' => $validation,
        '#DISCONFIRMATION-URL#' => $delete
    );

    $subst = array_merge($additional_subst, $widget->getDataOwnerSubst("\n", $petition));

    if ($subject_prefix) {
      $i18n = sfContext::getInstance()->getI18N();
      $i18n->setCulture($signing->getWidget()->getPetitionText()->getLanguageId());
      $translated = $i18n->__($subject_prefix);
      $subject = $translated . ' ' . $subject;
    }

    UtilMail::sendWithSubst($from, $to, $subject, $body, $petition_text, $widget, $subst, $signing->getSubst());
  }

}
