<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilTheme {

  public static $THEMES = array(
      null => 'Classic',
      2 => 'Sleek'
  );
  public static $CSS_FILES = array(
      2 => 'sleek.css'
  );

  /**
   * @param Widget $widget
   * @param Petition $petition
   */
  public static function printCss($widget, $petition) {

    $variables = null;
    if ($widget && $petition) {
      $variables = self::variables($widget, $petition);
    }

    $theme = $petition->getWidgetIndividualiseDesign() ? $widget->getThemeId() : $petition->getThemeId();

    $baseCss = file_get_contents(sfConfig::get('sf_web_dir') . '/css/dist/policat_widget_variables.css');
    if ($variables) {
      $baseCss = strtr($baseCss, $variables);
    }
    echo "\n<style type=\"text/css\">\n$baseCss\n</style>\n";

    if (array_key_exists($theme, self::$CSS_FILES)) {
      $css = file_get_contents(sfConfig::get('sf_web_dir') . '/css/dist/theme/' . self::$CSS_FILES[$theme]);
      if ($css) {
        if ($variables) {
          $css = strtr($css, $variables);
        }
        echo "\n<style type=\"text/css\">\n$css\n</style>\n";
      }
    }
  }

  public static function variables($widget, $petition) {
    $variables = array(
        'var(--font-family)' => $widget->getFontFamily()
    );
    $widget_colors = $petition->getWidgetIndividualiseDesign();
    foreach (WidgetTable::$STYLE_COLOR_NAMES as $style) {
      $var = 'var(--' . WidgetTable::$STYLE_COLOR_NAMES_CSS[$style] . ')';
      if ($widget_colors) {
        $variables[$var] = $widget->getStyling($style);
      } else {
        $variables[$var] = $petition['style_' . $style];
      }
    }

    return $variables;
  }

}
