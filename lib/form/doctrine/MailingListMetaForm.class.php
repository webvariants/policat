<?php

/**
 * MailingListMeta form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MailingListMetaForm extends BaseMailingListMetaForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('meta_' . $this->getObject()->getId() . '_[%s]');

    unset(
      $this['mailing_list_id'], $this['choices'], $this['data_json']
    );

    $this->setWidget('kind', new sfWidgetFormInputHidden());
    $this->setValidator('kind', new sfValidatorChoice(array('choices' => array_keys(MailingListMeta::$KIND_SHOW))));

    $this->setWidget('name', new sfWidgetFormInput(array(), array('size' => 90)));
    $this->getWidgetSchema()->setHelp('name', 'The following names will be translated: Company, Organisation, Party, Electorate, City, Post code');
    $this->setWidget('subst', new sfWidgetFormInput(array(), array('size' => 20)));
    $this->setValidator('subst', new sfValidatorRegex(
      array('pattern' => '/^#[A-Z-_0-9]+#$/', 'max_length' => 80, 'min_length' => 5, 'trim' => true), array('invalid' => 'Keyword must be of format "#MY-KEYWORD#"')
    ));
    $this->getWidgetSchema()->setLabel('subst', 'Keyword');
    $this->getWidgetSchema()->setHelp('subst', 'will be used as keyword in Emails and Target Selector');

    if ($this->getObject()->getKind() == MailingListMeta::KIND_CHOICE) {
      $this->setWidget('choices', new sfWidgetFormTextarea(array()));
      $this->setValidator('choices', new ValidatorList(array('required' => false)));
      if (!$this->getObject()->isNew()) {
        $choices = $this->getObject()->getMailingListMetaChoice();
        $default = array();
        foreach ($choices as $choice)
          $default[] = $choice->getChoice();
        $this->getWidgetSchema()->setDefault('choices', implode("\n", $default));
      }
    } elseif ($this->getObject()->getKind() == MailingListMeta::KIND_MAPPING) {
      $this->setWidget('mapping_id', new sfWidgetFormDoctrineChoice(array(
          'model' => 'Mapping',
          'default' => $this->getObject()->getMappingId(),
      )));
      $this->setValidator('mapping_id', new sfValidatorDoctrineChoice(array(
          'model' => 'Mapping'
      )));
      $this->getWidgetSchema()->setHelp('mapping_id', 'Select the database for your target list.');

      $meta_query = MailingListMetaTable::getInstance()->queryByMailingListChoice($this->getObject()->getMailingList());

      $this->setWidget('meta_id', new sfWidgetFormDoctrineChoice(array(
          'model' => 'MailingListMeta',
          'method' => 'getName',
          'default' => $this->getObject()->getMetaId(),
          'query' => $meta_query
      )));
      $this->setValidator('meta_id', new sfValidatorDoctrineChoice(array(
          'model' => 'MailingListMeta',
          'query' => $meta_query
      )));
      $this->getWidgetSchema()->setHelp('meta_id', 'Note that the options of that selector must be identical in spelling with the data used in the selected postcode database.');
    }

    if ($this->getObject()->getKind() == MailingListMeta::KIND_CHOICE) {
      $this->setWidget('multi', new sfWidgetCheckboxBootstrap(array(
          'label' => false,
          'inner_label' => 'more then one choice selectable per contact',
          'value_attribute_value' => 'yes'
      )));
      $this->setDefault('multi', $this->getObject()->getMulti());
      $this->setValidator('multi', new sfValidatorBoolean());
    }

    if ($this->getObject()->getKind() == MailingListMeta::KIND_MAPPING || $this->getObject()->getKind() == MailingListMeta::KIND_CHOICE) {
      $this->setWidget('typfield', new sfWidgetCheckboxBootstrap(array(
          'label' => false,
          'inner_label' => 'Select by typing',
          'value_attribute_value' => 'yes'
      )));
      $this->setDefault('typfield', $this->getObject()->getTypfield());
      $this->setValidator('typfield', new sfValidatorBoolean());
    }
  }

  protected function doSave($con = null) {
    parent::doSave($con);
    $this->getObject()->getMailingList()->invalidateCache();
    $choices_db = $this->getObject()->getMailingListMetaChoice();
    $choices = $this->getObject()->getKind() == MailingListMeta::KIND_CHOICE ? $this->getValue('choices') : array();
    $choices_lower = array_map('strtolower', $choices);
    $done = array();
    foreach ($choices_db as $choice_db) {
      $lower = mb_strtolower($choice_db['choice'], 'utf-8');
      $choice_key = array_search($lower, $choices_lower, true);
      if ($choice_key === false) {
        $choice_db->delete($con);
      } else {
        $done[] = $lower;
        if ($choices[$choice_key] !== $choice_db['choice']) {
          $choice_db['choice'] = $choices[$choice_key];
          $choice_db->save($con);
        }
      }
    }
    foreach ($choices as $choice) {
      if (!in_array(mb_strtolower($choice, 'utf-8'), $done, true)) {
        $choice_db = new MailingListMetaChoice();
        $choice_db->setMailingListMetaId($this->getObject()->getId());
        $choice_db->setChoice($choice);
        $choice_db->save();
      }
    }
  }

}
