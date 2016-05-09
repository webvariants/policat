<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetsCopyFollowForm extends sfForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('widgets_copy[%s]');

    $petition = $this->getOption('petition');
    $user = $this->getOption('user');
    $query = PetitionTable::getInstance()->queryByCampaign($petition->getCampaign())->andWhere('p.id != ?', $petition->getId());

    $this->setWidget('copy_id', new sfWidgetFormDoctrineChoice(array(
        'add_empty' => 'Select source action',
        'model' => 'Petition',
        'query' => $query,
        'label' => false
    ), array(
        'class' => 'add_popover',
        'data-content' => 'This function allows you to quickly import the layout and ownership status of a large number of widgets from a previous action. Owners of copied widgets will also be owners of the new copy. The function creates a copy of each widget from the selected action, as long as you have already created and activated a translation in the respective language of the widget. It doesn\'t import content (translations), data or the counter reading. You can use this function multiple times (it ignores widgets that you imported already). To forward the widget-URLs of the original widgets to the new copies, go to the original action and activate the forwarding.'
    )));

    $this->setValidator('copy_id', new sfValidatorDoctrineChoice(array(
        'model' => 'Petition',
        'query' => $query,
        'required' => true
    )));
  }

  public function copy() {
    $timeout = time() + 10;
    $widget_table = WidgetTable::getInstance();
    $target_petition = $this->getOption('petition'); /* @var $target_petition Petition */
    $source_petition_id = $this->getValue('copy_id');
    $source_petition = PetitionTable::getInstance()->findById($source_petition_id);
    $source_widget_ids = WidgetTable::getInstance()->fetchIdsOfPetition($source_petition);
    $exiting_origin_ids = WidgetTable::getInstance()->fetchOriginIdsOfPetition($target_petition);
    
    // Source Text --> Language --> Target Text

    $target_lang_to_text = array();
    $target_text_by_id = array();
    foreach ($target_petition->getPetitionText() as $text) {
      if ($text->getStatus() != PetitionText::STATUS_ACTIVE) {
        continue;
      }
      /* @var $text PetitionText */
      $target_lang_to_text[$text->getLanguageId()] = $text->getId();
      $target_text_by_id[$text->getId()] = $text;
    }

    $source_text_to_target_text = array();
    foreach ($source_petition->getPetitionText() as $text) {
      /* @var $text PetitionText */
      if (array_key_exists($text->getLanguageId(), $target_lang_to_text)) {
        $source_text_to_target_text[$text->getId()] = $target_lang_to_text[$text->getLanguageId()];
      }
    }

    $created = 0;
    $skip_language = 0;
    $skip_existing_origin = 0;
    $skip_timeout = 0;
    foreach ($source_widget_ids as $source_widget_id) {
      if ($skip_timeout || time() > $timeout) {
        $skip_timeout++;
        continue;
      }

      $source_widget = $widget_table->findOneById($source_widget_id); /* @var $source_widget Widget */
      if (!array_key_exists($source_widget->getPetitionTextId(), $source_text_to_target_text)) {
        $skip_language++;
        continue;
      }
      
      if (in_array($source_widget->getId(), $exiting_origin_ids)) {
        $skip_existing_origin++;
        continue;
      }

      $target_widget = new Widget();
      foreach (array('status', 'stylings', 'email', 'organisation',
        'landing_url', 'ref', 'validation_kind', 'validation_data', 'validation_status',
        'edit_code', 'paypal_email', 'user_id', 'data_owner') as $field) {
        $target_widget[$field] = $source_widget[$field];
      }
      $target_widget->setCampaignId($target_petition->getCampaignId());
      $target_widget->setPetitionId($target_petition->getId());
      $target_widget->setPetitionTextId($source_text_to_target_text[$source_widget->getPetitionTextId()]);
      $target_widget->setOriginWidgetId($source_widget->getId());

      if ($target_petition->getWidgetIndividualiseText()) {
        $text = $target_text_by_id[$target_widget->getPetitionTextId()];
        foreach (array('title', 'target', 'background', 'intro', 'footer', 'email_subject',
          'email_body') as $field) {
          $target_widget[$field] = $text[$field];
        }
      }

      $target_widget->save();
      $source_widget->free();

      $created++;
    }
    return array(
        'created' => $created,
        'skip_language' => $skip_language,
        'skip_existing_origin' => $skip_existing_origin,
        'skip_timeout' => $skip_timeout,
        'source_is_following' => $source_petition->getFollowPetitionId() == $target_petition->getId(),
        'source_petition_id' => $source_petition->getId()
    );
  }

}
