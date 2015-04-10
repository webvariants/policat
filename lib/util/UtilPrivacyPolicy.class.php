<?php
class UtilPrivyPolicy
{
  public static function get($language = 'en', $kind = null)
  {
    $pp_path = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'privacy_policy' . DIRECTORY_SEPARATOR;
    $pps = array();
    if (is_scalar($kind) && strlen($kind))
    {
      $pps[] = "$language$kind.txt";
      $pps[] = "en$kind.txt";
    }
    $pps[] = "$language.txt";
    $pps[] = "en.txt";
    foreach ($pps as $pp)
      if (file_exists($pp_path . $pp))
        return file_get_contents($pp_path . $pp);
    return 'No privacy policy found.';
  }
}