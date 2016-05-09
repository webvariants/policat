<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ContactTicketForm extends BaseForm {

  const OPTION_USER_ID = 'user_id';

  public function configure() {
    $this->disableLocalCSRFProtection();
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('contactticket[%s]');

    $this->setWidget('from', new sfWidgetFormInputText(array(
        'label' => 'Your e-mail'
      ), array(
        'class' => 'span8'
    )));
    $this->setValidator('from', new ValidatorEmail(array('required' => true)));

    $subjects = array(
        'Bug report',
        'Feature request / special offer',
        'Support'
    );

    $this->setWidget('subject', new sfWidgetFormChoice(array(
        'choices' => array_combine($subjects, $subjects)
      ), array(
        'class' => 'span8'
    )));
    $this->setValidator('subject', new sfValidatorChoice(array(
        'choices' => $subjects,
        'required' => true
    )));

    $this->setWidget('body', new sfWidgetFormTextarea(array(
        'label' => 'Message',
      ), array(
        'class' => 'span8'
    )));
    $this->setValidator('body', new sfValidatorString(array('min_length' => 1, 'max_length' => 20000, 'required' => true)));

    $this->setWidget('name', new sfWidgetFormInputText());
    $this->setValidator('name', new sfValidatorPass(array('required' => false)));

    $this->setWidget('message', new sfWidgetFormInputText());
    $this->setValidator('message', new sfValidatorPass(array('required' => false)));
  }

  public function save() {
    if ($this->getValue('name') || $this->getValue('message')) {
      return;
    }
    $user = sfGuardUserTable::getInstance()->findOneById($this->getOption(self::OPTION_USER_ID));

    $ticket = TicketTable::getInstance()->generate(array(
        TicketTable::CREATE_KIND => TicketTable::KIND_CONTACT_MESSAGE,
        TicketTable::CREATE_TEXT => $this->getValue('from') . "\n" . $this->getValue('subject') . "\n" . $this->getValue('body'),
        TicketTable::CREATE_TO => $user
    ));
    if ($ticket) {
      $ticket->save();
      $ticket->notifyAdmin('PoliCAT: ' . $this->getValue('subject'), $this->getValue('from'));
    }
  }

}
