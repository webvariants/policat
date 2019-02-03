<?php

class WidgetFormI18nChoiceCountry extends sfWidgetFormChoice
{
  static $FIX_CODES = array(
      'eu' => array(
          'GB' => 'Erresuma Batua'
      )
  );
  
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('culture');
    $this->addOption('countries');
    $this->addOption('add_empty', false);

    // populate choices with all countries
    $culture = isset($options['culture']) ? $options['culture'] : 'en';

    $countries = sfCultureInfo::getInstance($culture)->getCountries();
    if (isset($options['countries'])) {
      $f = array_combine($options['countries'], $options['countries']);
      $found = array_intersect_key($countries, $f);
      $missing = array_diff_key($f, $found);
      $missing_fix = sfCultureInfo::getInstance('en')->getCountries(array_keys($missing));
      $countries = array_merge($found, $missing_fix);
      if (array_key_exists($culture, self::$FIX_CODES)) {
        foreach (self::$FIX_CODES[$culture] as $iso => $translation) {
          if (array_key_exists($iso, $countries)) {
            $countries[$iso] = $translation;
          }
        }
      }
      natsort($countries);
    }

    $addEmpty = isset($options['add_empty']) ? $options['add_empty'] : false;
    if (false !== $addEmpty)
    {
      $countries = array_merge(array('' => true === $addEmpty ? '' : $addEmpty), $countries);
    }

    $this->setOption('choices', $countries);
  }
}
