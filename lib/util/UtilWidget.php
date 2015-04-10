<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilWidget {

  public static function renderWidget($params) {
    $widget = WidgetTable::getInstance()->fetch($params[1]);
    if (!$widget || $widget->getStatus() != Widget::STATUS_ACTIVE) {
      return '';
    }

    $context = sfContext::getInstance();

    $petition = $widget->getPetition();
    $url = $context->getRouting()->generate('sign_hp', array('id' => $widget['id'], 'hash' => $widget->getLastHash(true)), true);

    $count = $petition->getCount(60);
    $target = $count . '-' . Petition::calcTarget($count, $petition->getTargetNum());

    $widget_id = $widget['id'];
    $stylings = json_decode($widget->getStylings(), true);
    $stylings['type'] = 'embed';
    $stylings['url'] = $url;
    $stylings['width'] = 'auto';
    $stylings['count'] = number_format($count, 0, '.', ',') . ' ' . ('people so far');
    $stylings['target'] = $target;
    return
      '<script type="text/javascript">'
      . self::getInitJS() . self::getAddStyleJS($widget_id, $stylings) . self::getWidgetHereJs($widget_id, false)
      . '</script>';
  }

  public static function getInitJS() {
    return 'var policat = typeof policat === "undefined" ? {widgets: []} : policat;';
  }

  public static function getAddStyleJS($id, $stylings) {
    return 'policat.widgets[' . $id . '] = ' .(is_array($stylings) ? json_encode($stylings) : $stylings) . ';';
  }

  public static function getWidgetHereJs($id, $open = false) {
    return 'policat.widget_here('. $id . ', '. json_encode($open).');';
  }

}
