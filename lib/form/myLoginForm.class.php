<?php

class myLoginForm extends sfGuardFormSignin {

  public function configure() {
    parent::configure();
    $this->disableLocalCSRFProtection();
    unset($this['remember']);

    $this->widgetSchema['username']->setLabel('Email address');
  }

}