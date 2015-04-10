<?php

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
        '#VALIDATION-URL#' => $validation
    );

    if ($subject_prefix) {
      $i18n = sfContext::getInstance()->getI18N();
      $i18n->setCulture($signing->getWidget()->getPetitionText()->getLanguageId());
      $translated = $i18n->__($subject_prefix);
      $subject = $translated . ' ' . $subject;
    }

    UtilMail::sendWithSubst(null, $from, $to, $subject, $body, $petition_text, $widget, $additional_subst, $signing->getSubst());
  }

}
