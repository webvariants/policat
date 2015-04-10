<?php

/**
 * Base project form.
 * 
 * @package    policat
 * @subpackage form
 * @author     Martin
 */
class BaseForm extends sfFormSymfony {

  protected $rendered_rows = array();

  public function renderRows($fields) {
    if (is_string($fields))
      $fields = func_get_args();
    $rows = array();
    foreach ($fields as $name) {
      $ignore_missing = false;
      if ($name[0] == '*') {
        $ignore_missing = true;
        $name = substr($name, 1);
      }

      if ($name[strlen($name) - 1] == '*') {
        $name = substr($name, 0, strlen($name) - 1);
        foreach ($this as $field_name => $field) {
          if ($field instanceof sfFormField) {
            if (strpos($field_name, $name) === 0 && !in_array($field_name, $this->rendered_rows)) {

              $rows[] = $field->renderRow();
              $this->rendered_rows[] = $field_name;
            }
          }
        }
      } else {
        $this->rendered_rows[] = $name;

        if ($this->offsetExists($name))
          $rows[] = $this->offsetGet($name)->renderRow();
        else {
          if (!$ignore_missing)
            $rows[] = '<div><strong>missing:' . $name . '</strong></div>';
        }
      }
    }
    return implode("\n", $rows);
  }

  public function renderOtherRows() {
    $rows = array();
    foreach ($this as $name => $field) {
      if ($field instanceof sfFormField) {
        if (!$field->isHidden() && !in_array($name, $this->rendered_rows)) {
          $rows[] = $field->renderRow();
        }
      }
    }

    return implode("\n", $rows);
  }

}
