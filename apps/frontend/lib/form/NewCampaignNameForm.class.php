<?php

class NewCampaignNameForm extends sfForm {

  public function setup() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('new_campaign_name[%s]');

    $this->setWidget('name', new sfWidgetFormInputText(
      array('label' => false), array(
        'class' => 'input-medium add_popover popover_left',
        'placeholder' => 'Enter name of new campaign',
        'data-content' => 'Create a new campaign for your issue. Within each campaign, you can start as many actions as you like - simultaneously or consecutively. Grow your constituency within your campaign. Consider joining the campaign of your group or organisation before creating a new campaign.'
      ))
    );

    $this->setValidator('name', new sfValidatorAnd(array(
        new sfValidatorString(array(
            'min_length' => 3,
            'max_length' => 100,
            'required' => true,
            'trim' => true
          )),
        new sfValidatorDoctrineUnique(array(
            'required' => true,
            'model' => 'Campaign',
            'primary_key' => 'id',
            'column' => 'name'
          ))
    )));
  }

}