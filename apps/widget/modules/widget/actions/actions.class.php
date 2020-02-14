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
 * widget actions.
 *
 * @package    policat
 * @subpackage widget
 * @author     Martin
 *
 * @property $petition Petition
 * @property $widget Widget
 * @property $petition_text PetitionText
 */
class widgetActions extends policatActions
{
  public function showError($message, $json = null)
  {
    $this->setTemplate('error');
    $this->message = $message;
    if ($this->getRequest()->isMethod('post') || $json === true)
    {
      $this->getResponse()->setContentType('text/javascript');
      $this->setLayout(false);
      $this->json = true;
    }
    else
    {
      $this->json = false;
    }
  }

  protected function fetchWidget($force_id = false)
  {
    $id = $force_id === false ?  $this->getRequest()->getParameter('id') : $force_id;
    if (!is_numeric($id)) return $this->showError('Invalid ID');

    $this->widget = WidgetTable::getInstance()->fetch($id);
    if (!$this->widget) {
      $response = $this->getResponse();
      $response->setContent('// NOT FOUND');
      if ($response instanceof sfWebResponse) {
        $response->setHttpHeader('cache-control', 'public, must-revalidate, max-age=60');
      }
      $response->send();
      throw new sfStopException();
    }
    $this->widget->getPetition()->state(Doctrine_Record::STATE_CLEAN); // petition can not have changed yet, stupid doctrine

    if (empty ($this->widget)) return $this->forward404('No widget found');

    $this->setContentTags($this->widget);
    $this->addContentTags($this->widget->getCampaign());
    $this->addContentTags($this->widget->getPetition());
    $this->addContentTags($this->widget->getPetitionText());

    $donations_paypal_strore = StoreTable::getInstance()->findByKeyCached(StoreTable::DONATIONS_PAYPAL);
    if ($donations_paypal_strore)
      $this->addContentTags ($donations_paypal_strore);
  }

  public function executeSign(sfWebRequest $request)
  {
    // hash check
    $id = $request->getParameter('id');
    $hash = $request->getParameter('hash');
    if (!is_numeric($id) || !is_string($hash)) {
      $this->forward404();
    }
    $id = ltrim($id, ' 0');
    if (!Widget::isValidLastHash($id, $hash)) {
      $this->forward404();
    }

    $this->setLayout(false);
    $this->fetchWidget();

    $this->petition = $this->widget['Petition'];
    $this->petition_text = $this->widget['PetitionText'];
    $this->lang = $this->petition_text['language_id'];
    $this->getUser()->setCulture($this->lang);
    $widget_texts = $this->petition->getWidgetIndividualiseText();

    $this->petition_title = $this->petition['name'];
    $this->title         = $widget_texts ? $this->widget['title'] : $this->petition_text['title'];
    $this->target        = $widget_texts ? $this->widget['target'] : $this->petition_text['target'];
    $this->background    = $widget_texts ? $this->widget['background'] : $this->petition_text['background'];
    $this->social_share_text = $this->widget['social_share_text'] ? : $this->petition_text['social_share_text'];

    // donate_url on petition enables/disabled donate_url and donate_text feature
    $this->donate_url    = $this->petition['donate_url'] && $this->petition_text['donate_url'] ? $this->petition_text['donate_url'] : $this->petition['donate_url'];
    $this->donate_text   = $this->donate_url ? $this->petition_text['donate_text'] : null;
    if ($this->petition['donate_url'] && $this->petition['donate_widget_edit']) {
      if ($this->widget['donate_url']) {
        $this->donate_url = $this->widget['donate_url'];
      }
      $this->donate_text = $this->widget['donate_text'];
    }
    $this->donate_direct = $this->donate_url && !$this->donate_text;

    $paypal_email = $this->widget->getFinalPaypalEmail();
    $this->paypal_email  = StoreTable::value(StoreTable::DONATIONS_PAYPAL) && !$this->donate_url ? $paypal_email: '';
    if ($paypal_email === false) { // disable donate
      $this->donate_url = null;
    }
    $this->paypal_ref    = sprintf("%s%s%s", $this->petition['id'], $this->petition_text['language_id'], $this->widget['id']);
    $this->read_more_url = $this->widget['read_more_url'] ? : ($this->petition_text['read_more_url'] ? : $this->petition['read_more_url']);

    $this->require_billing = $this->widget->getRequireBilling();

    $this->width = $this->widget->getStyling('width');
    $this->font_css_file = UtilFont::cssFileByFont($this->widget->getFontFamily());

    $sign                    = new PetitionSigning();
    $sign['Petition']        = $this->widget['Petition'];
    $sign['petition_status'] = $this->widget['Petition']['status'];
    $sign['petition_enabled'] = $this->widget['Petition']['status'] != 7 ? 1 : 0;
    $sign['Widget']          = $this->widget;
    $sign['PetitionText']    = $this->petition_text;
    $sign['campaign_id']     = $this->widget['campaign_id'];
    $sign['language_id']     = $this->lang;

    if ($request->isMethod('post') && $request->hasParameter('widget') )
    {
      $form_param = $request->getParameter('widget');
      if (is_scalar($form_param['edit_code']) && !empty($form_param['edit_code']))
      {
        $new_widget = Doctrine_Core::getTable('Widget')->createQuery('w')
          ->where('w.id = ?', $id)
          ->andWhere('w.edit_code = ?', $form_param['edit_code'])
          ->addFrom('w.Campaign, w.Petition, w.PetitionText')
          ->fetchOne();
      }
    }

    if (!isset($new_widget))
    {
      $new_widget                    = new Widget();
      $new_widget['Parent']          = $this->widget;
      $new_widget['Campaign']        = $this->widget['Campaign'];
      $new_widget['Petition']        = $this->widget['Petition'];
      $new_widget['PetitionText']    = $this->widget['PetitionText'];
      $new_widget['email_targets']   = $this->widget['email_targets'];
      $new_widget['default_country'] = $this->widget['default_country'];
      $new_widget['email_validation_subject'] = $this->widget['email_validation_subject'];
      $new_widget['email_validation_body'] = $this->widget['email_validation_body'];
      $new_widget['privacy_policy_body'] = $this->widget['privacy_policy_body'];
      $new_widget['privacy_policy_url'] = $this->widget['privacy_policy_url'];
      $new_widget['read_more_url'] = $this->widget['read_more_url'];
      $new_widget['privacy_policy_link_text'] = $this->widget['privacy_policy_link_text'];
    }

    $subscribe_text = trim($this->petition_text['subscribe_text']);
    if ($this->widget->isInDataOwnerMode() && trim($this->widget['subscribe_text'])) {
      $subscribe_text = $this->widget['subscribe_text'];
    }
    if ($subscribe_text && mb_strpos($subscribe_text, '#') !== false) {
      $subscribe_text = strtr($subscribe_text, $this->widget->getDataOwnerSubst(' ', $this->petition));
    }

    $this->form          = new PetitionSigningForm($sign, array(
        'validation_kind'                           => PetitionSigning::VALIDATION_KIND_EMAIL,
        'subscribe_text'                            => $subscribe_text
    ));
    $this->form_embed    = new WidgetPublicForm($new_widget);

    $extra = array();

    if ($this->getRequest()->isMethod('post'))
    {
      $this->getResponse()->setContentType('text/javascript');
      $ajax_response_form = null;

      // It is a signing form
      if ($request->hasParameter($this->form->getName()))
      {
        if ($this->petition->isBefore() || $this->petition->isAfter() || $this->require_billing) {
          return $this->renderText(json_encode(array('over' => true)));
        }

        $ajax_response_form = $this->form;
        $this->form->bind($request->getPostParameter($this->form->getName()));
        if ($this->form->isValid())
        {
          $this->form->save();
          if (sfConfig::get('sf_environment') === 'stress') // ONLY FOR STRESS TEST !!!
            $extra['code'] = $this->form->getObject()->getId() . '-' . $this->form->getObject()->getValidationData();

          if ($this->form->getRefCode()) {
            $extra['ref_id'] = $this->form->getObject()->getId();
            $extra['ref_code'] = $this->form->getRefCode();
          }

          $search_table = PetitionSigningSearchTable::getInstance();
          $search_table->savePetitionSigning($sign, false);

          $con = $search_table->getConnection();
          $ref = $this->form->getValue(Petition::FIELD_REF);
          if (!(strpos($ref, 'http://') === 0 || strpos($ref, 'http://') === 0)) {
            $ref = null;
          }
          $sql_time = gmdate('Y-m-d H:i:s');
          // DQL query would invalidate petition cache so let's use SQL
          $con->exec('update petition set activity_at = ? where id = ?', array($sql_time, $this->petition->getId()));
          if ($ref) {
            $con->exec('update widget set activity_at = ?, last_ref = ? where id = ?', array($sql_time, $ref, $this->widget->getId()));
          } else {
            $con->exec('update widget set activity_at = ? where id = ?', array($sql_time, $this->widget->getId()));
          }
        }
      }

      // It is an embed this form
      else if ($request->hasParameter($this->form_embed->getName()))
      {
        $ajax_response_form = $this->form_embed;
        $this->form_embed->bind($request->getPostParameter($this->form_embed->getName()));
        if ($this->petition->getShowEmbed() && $this->form_embed->isValid()) {
          $this->form_embed->save();
          $extra['id'] = $this->form_embed->getObject()->getId();
          $extra['edit_code'] = $this->form_embed->getObject()->getEditCode();
          $extra['markup'] = UtilLink::widgetMarkup($extra['id']);
        } else {

        }
      }

      // It is a target selector
      else if ($request->hasParameter('target_selector')) {
        $target_selector = $request->getParameter('target_selector');
        if (is_scalar($target_selector)) {
          $ts_data = $this->petition->getTargetSelectorChoices($target_selector);
          ContactTable::getInstance()->mergeKeywordSubst($ts_data, $this->petition, $this->lang);
          return $this->renderText(json_encode($ts_data));
        }
      }

      // It is a pledges with two target selectors
      else if ($request->hasParameter('target_selector1') && $request->hasParameter('target_selector2')) {
        $target_selector1 = $request->getParameter('target_selector1');
        $target_selector2 = $request->getParameter('target_selector2');
        if (is_scalar($target_selector1) && is_scalar($target_selector2)) {
          $ts_data = $this->petition->getTargetSelectorChoices2($target_selector1, $target_selector2);
          ContactTable::getInstance()->mergeKeywordSubst($ts_data, $this->petition, $this->lang);
          return $this->renderText(json_encode($ts_data));
        }
      }

      return $this->renderPartial('json_form', array('form' => $ajax_response_form, 'extra' => $extra));
    }

    if ($this->petition->getLastSignings() == PetitionTable::LAST_SIGNINGS_SIGN_CONFIRM) {
      $this->last_signings = PetitionSigningTable::getInstance()->lastSignings($this->petition->getId());
    } else {
      $this->last_signings = null;
    }

    $this->openECI = false;
    if ($this->petition->getKind() == Petition::KIND_OPENECI) {
        if ($this->petition->getOpeneciUrl() && $this->petition->getOpeneciChannel()) {
            $this->openECI = true;
        }
    }
  }

  public function executeSignHp(sfWebRequest $request) {
    $this->setTemplate('sign');
    if ($request->isMethod('post')) return $this->executeSign($request);
    $this->executeSign($request);
    $this->width = 540;
  }

  public function executeValidate(sfWebRequest $request)
  {
    if ($request->hasParameter('code'))
    {
      $idcode = $request->getParameter('code');
      if (is_string($idcode)) $idcode = explode('-', trim($idcode));
      if (is_array($idcode) && count($idcode) === 2)
      {
        list($this->id, $code) = $idcode;
        $this->id = ltrim($this->id, '0 ');
        $petition_signing = PetitionSigningTable::getInstance()->fetch($this->id);
        if (!empty($petition_signing))
        {
          $petition      = $petition_signing->getPetition();
          $widget        = $petition_signing->getWidget();
          $campaign      = $petition->getCampaign();
          $petition_text = $widget->getPetitionText();
          $this->lang    = $petition_text->getLanguageId();
          $this->getContext()->getI18N()->setCulture($this->lang);
          $this->getUser()->setCulture($this->lang);

          /* @var $petition_signing PetitionSigning */
          /* @var $petition Petition */

          $wave = null;
          if ($petition->isGeoKind()) {
            foreach ($petition_signing['PetitionSigningWave'] as $psw) {
              if ($psw['validation_data'] === $code) {
                $wave = $psw;
                break;
              }
            }
          }

          if ((hash_equals($code, $petition_signing->getValidationData()) && !$petition->isGeoKind()) || $wave)
          {
            $ref_code = 0;
            if (($petition->getKind() == Petition::KIND_OPENECI) && !$petition_signing->getRefShown()) {
              $ref_code = $petition_signing->addRefCode(1);
            }

            if ($petition_signing->getStatus() == PetitionSigning::STATUS_PENDING
              || ($wave && $wave->getStatus() == PetitionSigning::STATUS_PENDING)
              || ($petition_signing->getStatus() == PetitionSigning::STATUS_COUNTED && $petition_signing->getVerified() == PetitionSigning::VERIFIED_NO))
            {
              $quota_emails = 0;
              if ($petition->isEmailKind())
              {
                if ($petition->isGeoKind()) {
                  $petition_signing->setWaveCron($petition_signing->getWavePending());
                  $wave->setStatus(PetitionSigning::STATUS_COUNTED);
                }
                else {  // regular email action, send mail now
                  $subject = $petition_signing->getField(Petition::FIELD_EMAIL_SUBJECT);
                  $body    = $petition_signing->getField(Petition::FIELD_EMAIL_BODY);
                  $email_targets = $petition->getEmailTargets();
                  if (is_string($email_targets)) $email_targets = json_decode($email_targets, true);
                  if (is_array($email_targets) && count($email_targets)) {
                    $petition_text = $widget->getPetitionText()->getEmailBody();
                    if ($petition_text) {
                      UtilMail::appendMissingKeywords($body, $petition_text, PetitionSigningTable::$KEYWORDS);
                    }

                    /* Email to target */
                    UtilMail::send('Target-' . $petition->getCampaignId(), 'Targets-' . $petition->getId(), $petition_signing->getEmailContact($petition->getFromEmail(), true), $email_targets, $subject, /* email problem */
                      $body, null, $petition_signing->getSubst($this->lang), null, $petition_signing->getEmailContact());

                    $quota_emails = count($email_targets);
                  }
                }
              } else {
                $quota_emails = 1;
              }

              if ($quota_emails && StoreTable::value(StoreTable::BILLING_ENABLE) && $campaign->getBillingEnabled() && $campaign->getQuotaId()) {
                QuotaTable::getInstance()->useQuota($campaign->getQuotaId(), $quota_emails);
                $petition_signing->setQuotaId($campaign->getQuotaId());
                $petition_signing->setQuotaEmails($quota_emails);
              }

              $petition->state(Doctrine_Record::STATE_CLEAN); // prevent updating Petition for nothing
              $petition_signing->setStatus(PetitionSigning::STATUS_COUNTED);
              $petition_signing->setVerified(PetitionSigning::VERIFIED_YES);
              $petition_signing->setEmailHash($petition_signing->getEmailHashAuto());

              // Mail Export
              if ($petition->getMailexportEnabled()
                && $petition_signing->getSubscribe() == PetitionSigning::SUBSCRIBE_YES
                && $petition_signing->getMailexportPending() == PetitionSigning::MAILEXPORT_PENDING_NO) {
                $petition_signing->setMailexportPending(PetitionSigning::MAILEXPORT_PENDING_YES);
              }

              UtilThankYouEmail::send($petition_signing);
              $petition_signing->save();
            } else {
              if ($ref_code) {
                $petition_signing->save();
              }
            }

            $this->ref      = $petition_signing->getField(Petition::FIELD_REF);
            $this->wid      = $petition_signing->getWidgetId();
            $this->id       = $petition_signing->getId();
            $this->ref_code = $ref_code;

            $response = $this->getResponse();
            if ($response instanceof sfWebResponse) {
              $response->setHttpHeader('cache-control', 'private, must-revalidate, max-age=60');
            }

            $this->landing_url = $widget->findLandingUrl($petition, !!$this->ref_code);
            if ($this->landing_url) {
              $this->setLayout(false);
              $this->setTemplate('landing');
            } else {
              $this->petition_id = $petition->getId();
              $this->name = strtr((string) $petition_signing->getComputedName(), array('!' => '', '&' => '', ';' => '', '?' => ''));
              $this->backgroundColor = $widget->getStyling('bg_right_color');
            }
            return;
          }
        }
      }

      $this->setLayout(false);
      $this->setTemplate('fail');
    }
  }

  public function executeWidgetOuter(sfWebRequest $request)
  {
    $this->fetchWidget();
    $this->checkFollowWidget();
    $petition = $this->widget['Petition'];
    /* @var $petition Petition */
    $petition_text = $this->widget['PetitionText'];
    /* @var $petition_text PetitionText */

    $this->numberSeparator = $petition_text->utilCultureInfo()->getNumberFormat()->getGroupSeparator();
    if ($petition->getKind() == Petition::KIND_EMAIL_TO_LIST && $petition->getShowEmailCounter() == Petition::SHOW_EMAIL_COUNTER_YES) {
      $this->count = $petition->countMailsSent() + $petition->getAddnumEmailCounter();
      $this->count_translation = '# emails sent';
      $this->target = $this->count . '-' . Petition::calcTarget($this->count, $petition->getTargetNumEmailCounter());
    } else {
      if ($petition->getKind() == Petition::KIND_OPENECI && $petition->getOpeneciCounterOverride()) {
        $this->count = (int) $petition->getOpeneciCounterTotal();
      } else {
        $this->count = $petition->getCount(60);
      }
      $this->count_translation = '# Participants';
      $this->target = $this->count . '-' . Petition::calcTarget($this->count, $petition->getTargetNum());
    }
    $image_prefix = ($request->isSecure() ? 'https://' : 'http://') . $request->getHost() . '/' . $request->getRelativeUrlRoot() . 'images/';
    $image_prefix_static = ($request->isSecure() ? 'https://' : 'http://') . $request->getHost() . '/' . $request->getRelativeUrlRoot() . 'images_static/';

    $this->kind = $petition->getKind();
    $this->lang = $petition_text->getLanguageId();
    $this->getUser()->setCulture($this->lang);
    $this->headline = $petition->getLabel(PetitionTable::LABEL_TAB);

    $stylings = json_decode($this->widget->getStylings(), true);
    if (!is_array($stylings)) {
      $stylings = array();
    }
    $widget_colors = $petition->getWidgetIndividualiseDesign();
    foreach (array('title_color', 'body_color', 'button_color', 'bg_left_color', 'bg_right_color', 'form_title_color') as $style) {
      if (!$widget_colors || !isset($stylings[$style]) || !$stylings[$style]) {
        $stylings[$style] = $petition['style_' . $style];
      }
    }
    if (!array_key_exists('width', $stylings)) {
        $stylings['width'] = 'auto';
    }
    UtilTheme::addWidgetStyles($stylings, $this->widget, $petition);
    $this->stylings = $stylings;

    $this->keyvisual = $petition->getKeyVisual() ? $image_prefix . 'keyvisual/' . $petition->getKeyVisual() :  null;
    $this->sprite = $image_prefix_static . 'policat.spr.png';
    $this->url = $this->getContext()->getRouting()->generate('sign', array('id' => $this->widget['id'], 'hash' => $this->widget->getLastHash(true)), true);
    $this->getResponse()->setContentType('text/javascript');
    $this->setLayout(false);

    $title = $this->widget->getTitle();
    if (!$petition->getWidgetIndividualiseText()) {
      $title = $petition_text->getTitle();
    }

    $this->title = Util::enc($title);
  }

  private function checkFollowWidget() {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['noRedirect']) && $route_params['noRedirect']) {
      return;
    }
    $check_cycle = array();
    while (1) {
      $petition = $this->widget['Petition'];
      /* @var $petition Petition */

      if ($petition->getFollowPetitionId()) {
        $follow_widget_id = WidgetTable::getInstance()->fetchWidgetIdByOrigin($petition->getFollowPetitionId(), $this->widget->getId());
        if (in_array($follow_widget_id, $check_cycle)) {
          return;
        } else {
          $check_cycle[] = $follow_widget_id;
        }
        $this->fetchWidget($follow_widget_id);
      } else {
        return;
      }
    }
  }

  public function executeWidgetedit(sfWebRequest $request)
  {
    if ($request->hasParameter('code'))
    {
      $idcode = $request->getParameter('code');
      if (is_string($idcode)) $idcode = explode('-', trim($idcode));
      if (is_array($idcode) && count($idcode) === 2)
      {
        list($this->id, $this->code) = $idcode;
        $this->id = ltrim($this->id, '0 ');
        $widget = Doctrine_Core::getTable('Widget')
          ->createQuery('w')
          ->where('w.id = ?', $this->id)
          ->leftJoin('w.PetitionText pt')
          ->select('w.*, pt.id, pt.language_id')
          ->fetchOne();
        if (!empty($widget))
        {
          $this->lang = $widget->getPetitionText()->getLanguageId();
          $this->getContext()->getI18N()->setCulture($this->lang);
          $this->getUser()->setCulture($this->lang);

          if (hash_equals($this->code, $widget->getEditCode()))
          {
            $this->width = $widget->getStyling('width');
            return;
          }
        }
      }
      return $this->showError('Wrong edit code.');
    }
  }

  public function executeText(sfWebRequest $request)
  {
    $hash = $request->getParameter('hash');
    $id = PetitionText::getIdByHash($hash);

    if ($id === false) $this->forward404();

    $petition_text = Doctrine_Core::getTable('PetitionText')
      ->createQuery('pt')
      ->where('pt.id = ?', $id)
      ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
      ->fetchOne();

    $result = array();
    foreach (array('title', 'target', 'background', 'intro', 'body', 'footer', 'email_subject', 'email_body') as $field)
      $result[$field] = $petition_text[$field];

    $this->getResponse()->setContentType('text/javascript');
    $this->setLayout(false);

    $this->setContentTags($petition_text);

    return $this->renderText(json_encode($result));
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->setLayout(false);
    if ($request->hasParameter('code'))
    {
      $idcode = $request->getParameter('code');
      if (is_string($idcode)) $idcode = explode('-', trim($idcode));
      if (is_array($idcode) && count($idcode) === 2)
      {
        list($this->id, $code) = $idcode;
        $this->id = ltrim($this->id, '0 ');
        $petition_signing = PetitionSigningTable::getInstance()->fetch($this->id);
        if (!empty($petition_signing))
        {
          $widget        = $petition_signing->getWidget();
          $this->lang    = $widget->getPetitionText()->getLanguageId();
          $this->getContext()->getI18N()->setCulture($this->lang);
          $this->getUser()->setCulture($this->lang);

          /* @var $petition_signing PetitionSigning */
          /* @var $petition Petition */

          if ($code && hash_equals($code, $petition_signing->getDeleteCode()))
          {
            $petition_signing->delete();
            return;
          }
        }
      }
    }

    $this->setTemplate('fail');
  }

  public function executeUnsubscribe(sfWebRequest $request)
  {
    $this->setLayout(false);
    if ($request->hasParameter('code'))
    {
      $idcode = $request->getParameter('code');
      if (is_string($idcode)) $idcode = explode('-', trim($idcode));
      if (is_array($idcode) && count($idcode) === 2)
      {
        list($this->id, $code) = $idcode;
        $this->id = ltrim($this->id, '0 ');
        $petition_signing = PetitionSigningTable::getInstance()->fetch($this->id);
        if (!empty($petition_signing))
        {
          $widget        = $petition_signing->getWidget();
          $this->lang    = $widget->getPetitionText()->getLanguageId();
          $this->getContext()->getI18N()->setCulture($this->lang);
          $this->getUser()->setCulture($this->lang);

          /* @var $petition_signing PetitionSigning */
          /* @var $petition Petition */

          if ($code && hash_equals($code, $petition_signing->getDeleteCode()))
          {
            if ($petition_signing->getSubscribe() !== PetitionSigning::SUBSCRIBE_NO) {
              $petition_signing->setSubscribe(PetitionSigning::SUBSCRIBE_NO);
              $petition_signing->save();
            }
            return;
          }
        }
      }
    }

    $this->setTemplate('fail');
  }

  public function executeRefShown(sfWebRequest $request)
  {
    if (!$request->isMethod('POST')) {
      $this->forward404();
    }

    $id = $request->getParameter('id');
    $code = $request->getParameter('code');

    if (!$id || !$code || !is_scalar($id) || !is_scalar($code)) {
      $this->forward404();
    }

    $this->setTemplate(false);
    $this->setLayout(false);
    $this->getResponse()->setContentType('application/json');

    $table = PetitionSigningTable::getInstance();
    $hash = $table->fetchRefHash($id, $mailexport_pending);

    $data = array('status' => 'ok');
    if (!$hash) {
      $data = array('status' => 'no hash');
    } else {
      $code_ok = false;
      foreach (explode('; ', $hash) as $hash_i) {
        if (password_verify($code, $hash_i)) {
          $code_ok = true;
          break;
        }
      }

      if (!$code_ok) {
        $data = array('status' => 'wrong code');
      } else {
        if ($mailexport_pending == PetitionSigning::MAILEXPORT_PENDING_DONE) {
          // retrigger export when done before
          $mailexport_pending = PetitionSigning::MAILEXPORT_PENDING_YES;
        } else {
          $mailexport_pending = null;
        }

        $table->setRefShown($id, $mailexport_pending);
      }
    }

    sfConfig::set('sf_web_debug', false);
    return $this->renderText(json_encode($data));
  }

  public function executePage(sfWebRequest $request)
  {
    $this->fetchWidget();
    $this->backgroundColor = $this->widget->getStyling('bg_right_color');
  }
}
