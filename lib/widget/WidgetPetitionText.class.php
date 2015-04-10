<?php

class WidgetPetitionText extends sfWidgetFormChoice
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addRequiredOption('petition_id');
    $this->addRequiredOption('petition_text_id');
    $this->addOption('choices');
    $this->addOption('renderer_class', 'sfWidgetFormSelectPetitionTextHash');
  }

  public function getChoices()
  {
    $petition_texts = Doctrine_Core::getTable('PetitionText')
      ->createQuery('pt')
      ->leftJoin('pt.Language l')
      ->where('pt.petition_id = ?', $this->getOption('petition_id'))
      ->andWhere('pt.status = ? or pt.id = ?', array(PetitionText::STATUS_ACTIVE, $this->getOption('petition_text_id')))
      ->select('pt.id, l.id, l.name')
      ->orderBy('l.order_number')
      ->fetchArray();

    $language_choices = array();
    foreach ($petition_texts as $petition_text)
      $language_choices[PetitionText::getHashForId($petition_text['id'])] = $petition_text['Language']['name'];

    return $language_choices;
  }
}