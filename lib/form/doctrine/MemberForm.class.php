<?php

/**
 * Member form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MemberForm extends BaseMemberForm
{
  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('policat');
  }
}
