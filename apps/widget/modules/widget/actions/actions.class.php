<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
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

  protected function fetchWidget()
  {
    $id = $this->getRequest()->getParameter('id');
    if (!is_numeric($id)) return $this->showError('Invalid ID');

    $this->widget = WidgetTable::getInstance()->fetch($id);
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
    if (!is_numeric($id) || !is_string($hash)) $this->forward404();
    $id = ltrim($id, ' 0');
    if (!Widget::isValidLastHash($id, $hash)) $this->forward404();

    $this->setLayout(false);
    $this->fetchWidget();

    $this->petition = $this->widget['Petition'];
    $this->petition_text = $this->widget['PetitionText'];
    $this->lang = $this->petition_text['language_id'];
    $this->getUser()->setCulture($this->lang);
    $widget_texts = $this->petition->getWidgetIndividualiseText();

    $this->title         = ($widget_texts && !empty($this->widget['title'])) ? $this->widget['title'] : $this->petition_text['title'];
    $this->target        = $widget_texts ? $this->widget['target'] : $this->petition_text['target'];
    $this->background    = $widget_texts ? $this->widget['background'] : $this->petition_text['background'];

    $this->paypal_email  = StoreTable::value(StoreTable::DONATIONS_PAYPAL) ? $this->widget->getFinalPaypalEmail() : '';
    $this->paypal_ref    = sprintf("%s%s%s", $this->petition['id'], $this->petition_text['language_id'], $this->widget['id']);
    $this->read_more_url = $this->petition['read_more_url'];

    $this->width = $this->widget->getStyling('width');
    $this->font_family = $this->petition->getStyleFontFamily();
    $widget_colors = $this->petition->getWidgetIndividualiseDesign();
    foreach (array('title_color', 'body_color', 'button_color', 'bg_left_color', 'bg_right_color', 'form_title_color') as $style) {
      if ($widget_colors) {
        $this->$style = $this->widget->getStyling($style, $this->petition['style_' . $style]);
      } else {
        $this->$style = $this->petition['style_' . $style];
      }
    }

    $sign             = new PetitionSigning();
    $sign['Petition'] = $this->widget['Petition'];
    $sign['Widget']   = $this->widget;

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
      $new_widget                 = new Widget();
      $new_widget['Parent']       = $this->widget;
      $new_widget['Campaign']     = $this->widget['Campaign'];
      $new_widget['Petition']     = $this->widget['Petition'];
      $new_widget['PetitionText'] = $this->widget['PetitionText'];
    }

    $this->form          = new PetitionSigningForm($sign, array(
        'validation_kind'                           => PetitionSigning::VALIDATION_KIND_EMAIL
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
        if ($this->petition->isBefore() || $this->petition->isAfter()) {
          return $this->renderText(json_encode(array('over' => true)));
        }

        $ajax_response_form = $this->form;
        $this->form->bind($request->getPostParameter($this->form->getName()));
        if ($this->form->isValid())
        {
          $this->form->save();
          if (sfConfig::get('sf_environment') === 'stress') // ONLY FOR STRESS TEST !!!
            $extra['code'] = $this->form->getObject()->getId() . '-' . $this->form->getObject()->getValidationData();

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
        if ($this->form_embed->isValid()) {
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
        if (is_scalar($target_selector))
          return $this->renderText(json_encode($this->petition->getTargetSelectorChoices($target_selector)));
      }

      // It is a pledges with two target selectors
      else if ($request->hasParameter('target_selector1') && $request->hasParameter('target_selector2')) {
        $target_selector1 = $request->getParameter('target_selector1');
        $target_selector2 = $request->getParameter('target_selector2');
        if (is_scalar($target_selector1) && is_scalar($target_selector2))
          return $this->renderText(json_encode($this->petition->getTargetSelectorChoices2($target_selector1, $target_selector2)));
      }

      return $this->renderPartial('json_form', array('form' => $ajax_response_form, 'extra' => $extra));
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
          $this->lang    = $widget->getPetitionText()->getLanguageId();
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

          if (($code === $petition_signing->getValidationData() && !$petition->isGeoKind()) || $wave)
          {
            if ($petition_signing->getStatus() == PetitionSigning::STATUS_PENDING
              || ($wave && $wave->getStatus() == PetitionSigning::STATUS_PENDING))
            {
              if ($petition->isEmailKind())
              {
                if ($petition->isGeoKind()) {
                  $petition_signing->setWaveCron($petition_signing->getWavePending());
                  $wave->setStatus(PetitionSigning::STATUS_VERIFIED);
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
                    UtilMail::send(null, $petition_signing->getEmailContact($petition->getFromEmail(), true), $email_targets, $subject, /* email problem */
                      $body, null, $petition_signing->getSubst($this->lang), null, $petition_signing->getEmailContact());
                  }
                }
              }
              $petition_signing->setStatus(PetitionSigning::STATUS_VERIFIED);
              $petition_signing->setEmailHash($petition_signing->getEmailHashAuto());
              $petition_signing->save();
            }

            $this->ref      = $petition_signing->getField(Petition::FIELD_REF);
            $this->wid      = $petition_signing->getWidgetId();

            $this->landing_url = $widget->findLandingUrl($petition);
            if ($this->landing_url) {
              $this->setLayout(false);
              $this->setTemplate('landing');
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
    $petition = $this->widget['Petition'];
    /* @var $petition Petition */
    $petition_text = $this->widget['PetitionText'];
    /* @var $petition_text PetitionText */

    $this->count = $petition->getCount(60);
    $this->target = $this->count . '-' . Petition::calcTarget($this->count, $this->widget->getPetition()->getTargetNum());
    $image_prefix = ($request->isSecure() ? 'https://' : 'http://') . $request->getHost() . '/' . $request->getRelativeUrlRoot() . 'images/';

    $this->kind = $this->widget->getPetition()->getKind();
    $this->lang = $this->widget->getPetitionText()->getLanguageId();
    $this->getUser()->setCulture($this->lang);
    $this->label_mode = $this->widget->getPetition()->getLabelMode();

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
    $this->stylings = $stylings;

    $this->keyvisual = $this->widget->getPetition()->getKeyVisual() ? $image_prefix . 'keyvisual/' . $this->widget->getPetition()->getKeyVisual() :  null;
    $this->sprite = $image_prefix . 'policat.spr.png';
    $this->url = $this->getContext()->getRouting()->generate('sign', array('id' => $this->widget['id'], 'hash' => $this->widget->getLastHash(true)), true);
    $this->getResponse()->setContentType('text/javascript');
    $this->setLayout(false);

    $title = $this->widget->getTitle();
    if (!$petition->getWidgetIndividualiseText()) {
      $title = $petition_text->getTitle();
    }

    $this->title = Util::enc($title);
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

          if ($this->code == $widget->getEditCode())
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
}
