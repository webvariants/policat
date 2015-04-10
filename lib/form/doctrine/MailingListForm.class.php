<?php

/**
 * MailingList form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 */
class MailingListForm extends BaseMailingListForm
{
  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('target_list[%s]');

    unset(
      $this['created_at'],
      $this['updated_at'],
      $this['campaign_id'],
      $this['object_version'],
      $this['status']
    );

    $this->setWidget('name', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 2)));    

    if (!$this->getObject()->isNew())
    {
      $this->setWidget('updated_at', new sfWidgetFormInputHidden());
      $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $this->getObject()->getUpdatedAt())));
    }
  }
}
