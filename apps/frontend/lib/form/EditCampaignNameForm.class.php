<?php

class EditCampaignNameForm extends BaseCampaignForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('campaign_name[%s]');

    $this->useFields(array('name'));

    $this->setWidget('name', new sfWidgetFormInputText(
        array('label' => 'Name'),
        array('class' => '', 'placeholder' => 'Enter new name of campaign'))
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