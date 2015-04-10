<?php

/**
 * sfGuardChangeUserPasswordForm for changing a users password
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfGuardChangeUserPasswordForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class sfGuardChangeUserPasswordForm extends BasesfGuardChangeUserPasswordForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->validatorSchema['password']->setOption('min_length', 10);
    $this->validatorSchema['password']->setMessage('min_length', 'Password is too short (%min_length% characters min).');
  }
}