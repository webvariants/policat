<?php

/**
 * CampaignStore form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 */
class CampaignStoreForm extends BaseCampaignStoreForm
{
  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('cstore[%s]');
    
    $this->useFields(array('value'));
    
    $this->getWidget('value')->setAttribute('class', 'markdown highlight');
    $this->getWidgetSchema()->setLabel('value', 'Privacy Policy');
    $this->getWidgetSchema()->setHelp('value', '#DATA-OFFICER-NAME#, #DATA-OFFICER-ORGA#, #DATA-OFFICER-EMAIL#, #DATA-OFFICER-WEBSITE#, #DATA-OFFICER-PHONE#, #DATA-OFFICER-MOBILE#, #DATA-OFFICER-STREET#, #DATA-OFFICER-POST-CODE#, #DATA-OFFICER-CITY#, #DATA-OFFICER-COUNTRY#, #DATA-OFFICER-ADDRESS#');
  }
}
