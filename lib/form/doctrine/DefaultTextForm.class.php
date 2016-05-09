<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * DefaultText form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class DefaultTextForm extends BaseDefaultTextForm
{
  public function configure()
  {
    unset($this['text']);

    $this->widgetSchema->setFormFormatterName('policat');
    $this->setWidget('subject', new sfWidgetFormInput(array(), array('size' => 90)));
    $this->setWidget('body', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 30)));

    if ($this->getObject()->getLanguageId() === null)
    {
      $this->setWidget('language_id', new sfWidgetFormDoctrineChoice(array(
          'model' => $this->getRelatedModelName('Language'),
          'add_empty' => false,
          'query' => Doctrine_Core::getTable('Language')
          ->createQuery('l')
          ->where('l.id NOT IN (SELECT pp.language_id FROM DefaultText pp WHERE pp.text = ?)', $this->getObject()->getText())
      )));
      $this->setValidator('language_id', new sfValidatorDoctrineChoice(array(
          'model' => $this->getRelatedModelName('Language'),
          'query' => Doctrine_Core::getTable('Language')
          ->createQuery('l')
          ->where('l.id NOT IN (SELECT pp.language_id FROM DefaultText pp WHERE pp.text = ?)', $this->getObject()->getText())
      )));
    }
    else
    {
      unset($this['language_id']);
    }
  }
}
