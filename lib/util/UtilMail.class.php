<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilMail {

  public static function getDefaultFrom($overwrite_name = null) {
    $mail = StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_ADDRESS);
    $name = StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_NAME);


    if ($overwrite_name && is_array($overwrite_name)) {
      $possible_name = reset($overwrite_name);
      if ($possible_name) {
        $name = $possible_name;
      }
    }

    if ($name)
      return array($mail => $name);
    return $mail;
  }

  public static function useSender() {
    return StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_SENDER) ? true : false;
  }

  private static function fromOnlyVerified() {
    return StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_FROM_ONLY_VERIFIED) ? true : false;
  }

  private static $verified_cache = null;

  private static function isVerified($email) {
    $fullemail = is_array($email) ? key($email) : $email;

    if (!$fullemail) {
      return false;
    }

    $parts = explode('@', $fullemail);
    if (count($parts) !== 2) {
      return false;
    }

    $domain = $parts[1];

    if (self::$verified_cache === null) {
      $addressesStr = StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_VERIFIED, '');
      if (!$addressesStr) {
        self::$verified_cache = array();
        return false;
      }

      self::$verified_cache = preg_split('/\r\n|[\r\n]/', $addressesStr);
    }

    return in_array($fullemail, self::$verified_cache) || in_array($domain, self::$verified_cache);
  }

  public static function send($trackCampaign, $trackId, $from, $to, $subject, $body, $contentType = null, $subst = null, $subst_2nd = null, $replyTo = null, $attachments = array(), $markdown = false) {
    $message = Swift_Message::newInstance();
    if ($from == null) {
      $message->setFrom(self::getDefaultFrom());
    } else {
      if (!self::fromOnlyVerified() || self::isVerified($from)) {
        $message->setFrom($from);
      } else {
        $message->setFrom(self::getDefaultFrom($from));
        if (!$replyTo) {
          $message->setReplyTo($from);
        }
      }

      if (self::useSender()) {
        $message->setSender(self::getDefaultFrom());
      }
    }

    if ($subst && is_array($subst)) {
      $body = strtr($body, $subst);
      $subject = strtr($subject, $subst);
    }

    $body_html = null;

    if ($markdown) {
      if ($subst_2nd && is_array($subst_2nd)) {
        $forth = array();
        $back = array();
        $i = 0;
        $hash = mt_srand();

        foreach ($subst_2nd as $subst_key => $subst_value) {
          $i++;
          $forth[$subst_key] = 'PC123' . $subst_key . $hash . $i . 'PC123';
          $back[$forth[$subst_key]] = Util::enc($subst_value);
        }

        $body_html = strtr(UtilMarkdown::transform(strtr($body, $forth), true, false), $back);
      } else {
        UtilMarkdown::transform($body, true, false);
      }
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

    if ($body_html) {
      $message->addPart($body_html, 'text/html', 'UTF-8');
    }

    foreach ($attachments as $attachment) {
      $message->attach($attachment);
    }

    // add tracking headers
    $tracking = sfConfig::get('app_mail_tracking');
    if ($trackCampaign) {
      $trackCampaignHeader = (is_array($tracking) && array_key_exists('header', $tracking) && array_key_exists('campaign', $tracking['header'])) ? $tracking['header']['campaign'] : null;
      if ($trackCampaignHeader) {
        if (is_numeric($trackCampaign)) {
          $trackCampaign = 'Campaign-' . $trackCampaign;
        }

        if (array_key_exists('campaign_prefix', $tracking['header']) && $tracking['header']['campaign_prefix']) {
          $trackCampaign = $tracking['header']['campaign_prefix'] . '-' . $trackCampaign;
        }

        $message->getHeaders()->addTextHeader($trackCampaignHeader, $trackCampaign);
      }
    }

    if ($trackId) {
      $trackIdHeader = (is_array($tracking) && array_key_exists('header', $tracking) && array_key_exists('id', $tracking['header'])) ? $tracking['header']['id'] : null;
      if ($trackIdHeader) {
        $message->getHeaders()->addTextHeader($trackIdHeader, $trackId);
      }
    }

    sfContext::getInstance()->getMailer()->send($message);
  }

  public static function sendWithSubst($trackCampaign, $trackId, $from, $to, $subject, $body, $petition_text, $widget = null, $additional_subst = array(), $subst_2nd = array(), $markdown = false) {
    $subst = self::createSubstArray($petition_text, $widget);
    if (is_array($additional_subst)) {
      $subst = array_merge($subst, $additional_subst);
    }

    self::send($trackCampaign, $trackId, $from, $to, $subject, $body, null, $subst, $subst_2nd, null, array(), $markdown);
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
      'TITLE' => 'title', // deprecated
      'TARGET' => 'target', // deprecated
      'BACKGROUND' => 'background', // deprecated
      'INTRO' => 'intro', // deprecated
      'FOOTER' => 'footer', // deprecated
      'EMAIL-SUBJECT' => 'email_subject', // deprecated
      'EMAIL-BODY' => 'email_body', // deprecated
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

    /* @var $petition_text PetitionText */
    $petition = $petition_text->getPetition();
    /* @var $petition Petition */
    if ($petition->isEmailKind()) {
      $action_text = $subst['#EMAIL-SUBJECT#'] . "\n\n" . $subst['#EMAIL-BODY#'] . "\n";
    } else {
      $action_text = $subst['#INTRO#'] . "\n\n" . $subst['#BODY#'] . "\n\n" . $subst['#FOOTER#'] . "\n";
    }

    $subst['#ACTION-TEXT#'] = $action_text;

    return $subst;
  }

  public static function tellyourmail($widget, $petition, $petition_text, $url_ref, $url_readmore) {
    /* @var $widget Widget */
    /* @var $petition Petition */
    /* @var $petition_text PetitionText */
    $subject = $petition_text->getEmailTellyourSubject();
    $body = $petition_text->getEmailTellyourBody();

    $subst = array_merge(self::createSubstArray($petition_text, $widget), array(
        'URL-REFERER' => $url_ref, // deprecated
        'URL-READMORE' => $url_readmore, // deprecated
        '#REFERER-URL#' => $url_ref,
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
