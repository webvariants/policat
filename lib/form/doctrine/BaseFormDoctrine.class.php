<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Project form base class.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormBaseTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class BaseFormDoctrine extends sfFormDoctrine
{
  protected $saveManyToMany = array();

  public function setup()
  {
  }

  public function addSaveManyToMany($path, $manyRelations)
  {
    $this->saveManyToMany[] = array(
      'path'          => $path,
      'manyRelations' => $manyRelations
    );
  }

  protected function saveAllManyToMany($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    foreach ($this->saveManyToMany as $i)
      $this->saveManyToMany($i['path'], $i['manyRelations'], $con);
  }

  protected function saveManyToMany($path, $manyRelations, $con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $form = $this;
    foreach ($path as $key)
      if  ($form instanceof sfFormObject || true)
      {
        if (isset ($form->embeddedForms[$key]))
        {
          $form = $form->embeddedForms[$key];
        }
        else
        {
          $form = null;
          break;
        }
      }
      else
      {
        if (isset ($form[$key]))
          $form = $form[$key];
        else
        {
          $form = null;
          break;
        }
      }

    if (empty ($form)) return;

    $forms =
      ($form instanceof sfForm && !$form instanceof sfFormObject)
      ? $form->embeddedForms
      : array('' => $form);

    foreach ($forms as $form_id => $form)
    {
      foreach ($manyRelations as $field => $class)
      {
        $existing = $form->object->$class->getPrimaryKeys();
        $value_path = $path;
        if (($form_id) !== '') $value_path[] = $form_id;
        $value_path[] = $field;

        $values = $this->values;
        foreach ($value_path as $key)
          if (is_array($values) && isset ($values[$key]))
            $values = $values[$key];
          else
            $values = null;
        if (!is_array($values)) $values = array();

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
          $form->object->unlink($class, array_values($unlink));
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
          $form->object->link($class, array_values($link));
        }
      }
    }
  }

  protected function doSave($con = null)
  {
    $this->saveAllManyToMany($con);
    parent::doSave($con);
  }
}
