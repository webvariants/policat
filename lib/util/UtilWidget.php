<?php

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