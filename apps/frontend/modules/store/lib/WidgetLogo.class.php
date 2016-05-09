<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetLogo extends sfWidgetFormInputFileEditable {

  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);

    $this->setOption('file_src', '/images/store');
    $this->setOption('is_image', true);
    $this->setOption('with_delete', false);
    $this->setOption('template', '<div>%file%<br />%input%<br />%delete% %delete_label%</div>');
  }

}