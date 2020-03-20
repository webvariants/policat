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

  public static function send($trackCampaign, $trackId, $from, $to, $subject, $body, $contentType = null, $subst = null, $subst_escape = null, $replyTo = null, $attachments = array(), $markdown = false) {
    $message = new Swift_Message();
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
      $hash = md5($body);

      if ($subst_escape && is_array($subst_escape)) {
        $forth = array();
        $back = array();
        $i = 0;

        foreach ($subst_escape as $subst_key => $subst_value) {
          $i++;
          $forth[$subst_key] = 'PC123p0LiC4t' . $hash . $i . 'PC123';
          $back[$forth[$subst_key]] = Util::enc($subst_value);
        }

        $body_html = strtr(self::cacheMarkdown(strtr($body, $forth), $hash . 's', $forth), $back);
      } else {
        $body_html = self::cacheMarkdown($body, $hash);
      }

      $xml_utf8 = '<?xml version="1.0" encoding="utf-8"?>'; // force utf8 for umlauts
      $in = '<div class="spacer10"></div><!--[if mso]><center><table><tr><td width="600"><![endif]--><div class="main-out"><div class="main-in"><div class="main-start"></div>';
      $out = '</div></div><!--[if mso]></td></tr></table></center><![endif]--> <div class="spacer10"></div>';
      $inline = new \InlineStyle\InlineStyle($xml_utf8 . $in . $body_html . $out);
      $inline->applyStylesheet(UtilEmailLinks::generateEmailCss($markdown));
      $body_html = $inline->getHTML();
      $body_split = explode($xml_utf8, $body_html, 2);
      if (count($body_split) === 2) {
        $body_html = $body_split[1];
      }

      $body = strip_tags($body);
    }

    if ($subst_escape && is_array($subst_escape)) {
      $body = strtr($body, $subst_escape);
      $subject = strtr($subject, $subst_escape);
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

  public static function tellyourmail($widget, $petition_text, $url_ref, $url_readmore) {
    /* @var $widget Widget */
    /* @var $petition_text PetitionText */
    $subject = $petition_text->getEmailTellyourSubject();
    $body = $petition_text->getEmailTellyourBody();

    $subst = array_merge($widget->getSubst(), array(
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

  private static function cacheMarkdown($body, $key, $subst_keys = array()) {
    $cache_key = null;
    $vcm = sfContext::getInstance()->getViewCacheManager();
    if ($vcm instanceof sfViewCacheTagManager) {
      $tc = $vcm->getTaggingCache();

      if ($tc) {
        $cache_key = 'mailmarkdown_' . $key . '_' . md5(json_encode($subst_keys));
        $cached = $tc->get($cache_key);
        if ($cached) {
          return $cached;
        }
      }
    }

    $html = UtilMarkdown::transform($body, true, false, true);

    if ($cache_key) {
      $tc->set($cache_key, $html, 600);
    }

    return $html;
  }

}
