<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilMail {

  public static function getDefaultFrom() {
    $mail = StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_ADDRESS);
    $name = StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_NAME);

    if ($name)
      return array($mail => $name);
    return $mail;
  }

  public static function useSender() {
    return StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_SENDER) ? true : false;
  }

  public static function send($sender, $from, $to, $subject, $body, $contentType = null, $subst = null, $subst_2nd = null, $replyTo = null) {
    $message = Swift_Message::newInstance();
    if ($from == null) {
      $message->setFrom(self::getDefaultFrom());
      if ($sender) {
        $message->setSender($sender);
      }
    } else {
      $message->setFrom($from);
      $message->setReplyTo($from);
      if ($sender == null) {
        if (self::useSender())
          $message->setSender(self::getDefaultFrom());
      }
      else {
        $message->setSender($sender);
      }
    }

    if ($subst && is_array($subst)) {
      $body = strtr($body, $subst);
      $subject = strtr($subject, $subst);
    }

    if ($subst_2nd && is_array($subst_2nd)) {
      $body = strtr($body, $subst_2nd);
      $subject = strtr($subject, $subst_2nd);
    }

    if ($replyTo) {
      $message->setReplyTo($replyTo);
    }

    $subject = mb_substr(strtr($subject, array("\n" => ' ', "\r" => ' ', "\t" => ' ', "\0" => '', "\x0B" => ' ')), 0, 120, 'UTF-8');

    $message->setTo($to)
      ->setSubject($subject)
      ->setBody($body, $contentType);
    sfContext::getInstance()->getMailer()->send($message);
  }

  public static function sendWithSubst($sender, $from, $to, $subject, $body, $petition_text, $widget = null, $additional_subst = array(), $subst_2nd = array()) {
    $subst = self::createSubstArray($petition_text, $widget);
    if (is_array($additional_subst))
      $subst = array_merge($subst, $additional_subst);

    self::send($sender, $from, $to, $subject, $body, null, $subst, $subst_2nd);
  }

  public static function isEmpty($object, $field) {
    if (is_object($object) || is_array($object) && isset($object[$field])) {
      $field = $object[$field];
      if (!empty($field) && is_scalar($field)) {
        return !(strlen(trim($field)) > 0);
      }
    }
    return true;
  }

  public static function firstValue($widget, $petition_text, $field) {
    if (!self::isEmpty($widget, $field))
      return $widget[$field];
    if (!self::isEmpty($petition_text, $field))
      return $petition_text[$field];
    return '';
  }

  public static function createSubstArray($petition_text, $widget = null) {
    $subst = array();
    foreach (array(
      'TITLE' => 'title',  // deprecated
      'TARGET' => 'target',  // deprecated
      'BACKGROUND' => 'background',  // deprecated
      'INTRO' => 'intro',  // deprecated
      'FOOTER' => 'footer',  // deprecated
      'EMAIL-SUBJECT' => 'email_subject',  // deprecated
      'EMAIL-BODY' => 'email_body',  // deprecated
      '#TITLE#' => 'title',
      '#TARGET#' => 'target',
      '#BACKGROUND#' => 'background',
      '#INTRO#' => 'intro',
      '#FOOTER#' => 'footer',
      '#EMAIL-SUBJECT#' => 'email_subject',
      '#EMAIL-BODY#' => 'email_body'
    ) as $keyword => $field)
      $subst[$keyword] = self::firstValue($widget, $petition_text, $field);
    $subst['BODY'] = $petition_text['body'];  // deprecated
    $subst['#BODY#'] = $petition_text['body'];
    return $subst;
  }

  public static function tellyourmail($widget, $petition, $petition_text, $url_ref, $url_readmore)
  {
    /* @var $widget Widget */
    /* @var $petition Petition */
    /* @var $petition_text PetitionText */
    $subject          = $petition_text->getEmailTellyourSubject();
    $body             = $petition_text->getEmailTellyourBody();

    $subst = array_merge(self::createSubstArray($petition_text, $widget), array(
      'URL-REFERER'  => $url_ref,  // deprecated
      'URL-READMORE' => $url_readmore,  // deprecated
      '#REFERER-URL#'  => $url_ref,
      '#READMORE-URL#' => $url_readmore,
    ));

    $subject = strtr(strtr($subject, $subst), array("\n" => ' ', "\r" => ' ', "\t" => ' ', "\0" => '', "\x0B" => ' '));
    if (strlen($subject) > 200) {
      $subject = mb_strcut($subject, 0, 200);
    }
    $subject = rawurlencode($subject);

    $body = strtr($body, $subst);
    $body = rawurlencode($body);
    $body = substr($body, 0, 1850 - strlen($subject));

    $body_len = strlen($body);
    if ($body_len > 3) {
      if ($body[$body_len - 1] === '%') {
        $body = substr($body, 0, $body_len - 1);
      } elseif ($body[$body_len - 2] === '%') {
        $body = substr($body, 0, $body_len - 2);
      }
    }

    return array($subject, $body);
  }

  public static function appendMissingKeywords(&$text, $base_text, $keywords, $glue = "\n") {
    $first = true;
    foreach ($keywords as $keyword) {
      if (mb_strpos($base_text, $keyword, 0, 'UTF-8') !== false) {
        if (mb_strpos($text, $keyword, 0, 'UTF-8') === false) {
          if ($first) {
            $text .= "\n";
            $first = false;
          }
          $text .= $glue . $keyword;
        }
      }
    }
  }

}
