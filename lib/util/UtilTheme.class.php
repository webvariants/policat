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
      2 => 'Sleek',
      3 => 'Light',
      4 => 'Classic - modified',
      5 => 'Minimal Sleek',
      6 => 'Flat'
  );
  public static $CSS_FILES = array(
      2 => 'sleek.css',
      3 => 'light.css',
      4 => 'classic-modified.css',
      5 => 'minimal.css',
      6 => 'flat.css'
  );

  public static $MAX_WIDTH = array(
      // no entry leads to 1080px
      5 => '768px'
  );

  public static $DISABLE_KIND = array(
      Petition::KIND_OPENECI => array(null, 3, 4, 6)
  );

  public static function themesByKind($kind) {
    if (array_key_exists($kind, self::$DISABLE_KIND)) {
        return array_diff_key(self::$THEMES, array_combine(self::$DISABLE_KIND[$kind], self::$DISABLE_KIND[$kind]));
    }

    return $THEMES;
  }

  public static $REMOVE_CLASSIC_CSS = array(6);

  public static function removeClassicCss($widget, $petition) {
    $variables = null;
    if ($widget && $petition) {
      $variables = self::variables($widget, $petition);
    }

    $theme = self::getThemeId($widget, $petition);
    return in_array($theme, self::$REMOVE_CLASSIC_CSS);
  }

  /**
   * @param Widget $widget
   * @param Petition $petition
   */
  public static function printCss($widget, $petition) {

    $variables = null;
    if ($widget && $petition) {
      $variables = self::variables($widget, $petition);
    }

    $theme = self::getThemeId($widget, $petition);

    if (in_array($theme, self::$REMOVE_CLASSIC_CSS)) {
        $baseCss = null;
    } else {
        $baseCss = file_get_contents(sfConfig::get('sf_web_dir') . '/css/dist/policat_widget_variables.css');
    }
    if ($variables && $baseCss) {
      $baseCss = strtr($baseCss, $variables);
    }
    if ($baseCss) {
        echo "\n<style type=\"text/css\">\n$baseCss\n</style>\n";
    }

    if (array_key_exists($theme, self::$CSS_FILES)) {
      $css = '';
      $cssFiles = (array) self::$CSS_FILES[$theme];
      foreach ($cssFiles as $cssFile) {
        $css .= file_get_contents(sfConfig::get('sf_web_dir') . '/css/dist/theme/' . $cssFile);
      }
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

  /**
   * @param Widget $widget
   * @param Petition $petition
   */
  public static function getThemeId($widget, $petition) {
    return $petition->getWidgetIndividualiseDesign() ? $widget->getThemeId() : $petition->getThemeId();
  }

  /**
   * @param Widget $widget
   * @param Petition $petition
   */
  public static function addWidgetStyles(&$stylings, $widget, $petition) {
    $theme = self::getThemeId($widget, $petition);

    if (array_key_exists($theme, self::$MAX_WIDTH)) {
      $stylings['max_width'] = self::$MAX_WIDTH[$theme];
    }
  }
}
