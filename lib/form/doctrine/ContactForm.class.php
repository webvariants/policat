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
 * Contact form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ContactForm extends BaseContactForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('contact_' . $this->getObject()->getId() . '_[%s]');

    unset(
      $this['status'], $this['mailing_list_id'], $this['petition_signing_list'],
      $this['bounce'], $this['bounce_at'], $this['bounce_blocked'], $this['bounce_hard'], $this['bounce_related_to'], $this['bounce_error']
    );

    $countries_false = array_keys(sfCultureInfo::getInstance()->getCountries());
    $countries = array();
    foreach ($countries_false as $country)
      if (!is_numeric($country))
        $countries[] = $country;
    $countries = array_diff($countries, array('QU', 'ZZ', 'MF'));

    $this->setWidget('email', new sfWidgetFormInput(array(), array('size' => 80)));
    $this->setValidator('email', new ValidatorEmail(array('max_length' => 80)));

    $this->setWidget('gender', new sfWidgetFormChoice(array(
        'choices' => Contact::$GENDER_SHOW2,
        'expanded' => true,
        'renderer_class' => 'FormatterRadio'
    )));
    $this->setValidator('gender', new sfValidatorChoice(array('choices' => array_keys(Contact::$GENDER_SHOW))));

    $this->setWidget('firstname', new sfWidgetFormInput(array(), array('size' => 100)));
    $this->setWidget('lastname', new sfWidgetFormInput(array(), array('size' => 100)));

    $this->setWidget('country', new sfWidgetFormI18nChoiceCountry(array('countries' => $countries, 'add_empty' => true)));
    $this->setValidator('country', new sfValidatorI18nChoiceCountry(array('countries' => $countries)));

    $metas = $this->getObject()->getMailingList()->getMailingListMeta(); // TODO: optimize
    $contact_metas = $this->getObject()->getContactMeta();

    foreach ($metas as $meta) { /* @var $meta MailingListMeta */
      $name = 'meta_' . $meta['id'];
      $contact_meta = null;
      $contact_meta_multi = array();
      foreach ($contact_metas as $cc) {
        /* @var $cc ContactMeta */
        if ($cc['mailing_list_meta_id'] === $meta['id']) {
          $contact_meta = $cc;
          if ($meta['kind'] == MailingListMeta::KIND_CHOICE && $meta->getMulti()) {
            foreach ($contact_metas as $cc) {
              /* @var $cc ContactMeta */
              if ($cc['mailing_list_meta_id'] === $meta['id']) {
                $contact_meta_multi[] = $cc->getMailingListMetaChoiceId();
              }
            }
          }
          break;
        }
      }
      switch ($meta['kind']) {
        case MailingListMeta::KIND_CHOICE:
          $choices = array();
          foreach ($meta->getMailingListMetaChoice() as $choice)
            $choices[$choice['id']] = $choice['choice'];
          $this->setWidget($name, new sfWidgetFormChoice(array('choices' => $choices, 'multiple' => $meta->getMulti())));
          $this->getWidgetSchema()->setDefault($name);
          if (count($choices) < 7) {
            $this->getWidget($name)->setOption('expanded', true);
            if (!$meta->getMulti())
              $this->getWidget($name)->setOption('renderer_class', 'FormatterRadio');
          } else {
            $this->getWidget($name)->setOption('expanded', false);
          }
          $this->setValidator($name, new sfValidatorChoice(array('choices' => array_keys($choices), 'multiple' => $meta->getMulti(), 'required' => !$meta->getMulti())));
          if ($contact_meta !== null) {
            if ($meta->getMulti()) {
              $this->getWidgetSchema()->setDefault($name, $contact_meta_multi);
            } else {
              $this->getWidgetSchema()->setDefault($name, $contact_meta['mailing_list_meta_choice_id']);
            }
          }
          $this->getWidgetSchema()->setLabel($name, $meta['name']);
          break;
        case MailingListMeta::KIND_FREE:
          $this->setWidget($name, new sfWidgetFormInputText(array()));
          $this->setValidator($name, new sfValidatorString(array('required' => false)));
          if ($contact_meta !== null)
            $this->getWidgetSchema()->setDefault($name, $contact_meta['value']);
          $this->getWidgetSchema()->setLabel($name, $meta['name']);
          break;
      }
    }
  }

  protected function doSave($con = null) {
    parent::doSave($con);
    $this->getObject()->getMailingList()->invalidateCache();

    $metas = $this->getObject()->getMailingList()->getMailingListMeta(); // TODO: optimize
    $contact_metas = $this->getObject()->getContactMeta();

    foreach ($metas as $meta) { /* @var $meta MailingListMeta */
      $value = $this->getValue('meta_' . $meta['id']);

      if ($meta['kind'] == MailingListMeta::KIND_CHOICE && $meta->getMulti()) {
        if (!is_array($value))
          $value = array();
        $keep_multis = array();
        foreach ($contact_metas as $cc) {
          if ($cc['mailing_list_meta_id'] === $meta['id']) {
            /* @var $cc ContactMeta */
            if (in_array($cc->getMailingListMetaChoiceId(), $value)) {
              $keep_multis[] = $cc->getMailingListMetaChoiceId(); // keep
            } else {
              $cc->delete($con); // delete
            }
          }
        }
        foreach ($value as $value_i) {
          if (!in_array($value_i, $keep_multis)) {
            $contact_meta = new ContactMeta(); // create
            $contact_meta->setMailingListMetaId($meta['id']);
            $contact_meta->setContactId($this->getObject()->getId());
            $contact_meta->setMailingListMetaChoiceId($value_i);
            $contact_meta->save($con);
          }
        }
      } else {
        $contact_meta = null;
        foreach ($contact_metas as $cc) {
          if ($cc['mailing_list_meta_id'] === $meta['id']) {
            $contact_meta = $cc;
            break;
          }
        }
        if ($contact_meta === null) {
          $contact_meta = new ContactMeta();
          $contact_meta->setMailingListMetaId($meta['id']);
          $contact_meta->setContactId($this->getObject()->getId());
        }

        switch ($meta['kind']) {
          case MailingListMeta::KIND_CHOICE:
            $contact_meta->setMailingListMetaChoiceId($value);
            break;
          case MailingListMeta::KIND_FREE:
            $contact_meta->setValue($value);
            break;
        }

        $contact_meta->save($con);
      }
    }
  }

}
