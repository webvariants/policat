<?php
class Util
{
  static function distinctRelations($object, array $relations)
  {
    $collect = new UtilCollectObject($object, $relations);
    return $collect->getResult();
  }

  static function enc($text) {
    if ($text === null) return '';
    if (is_scalar($text))
      return htmlentities($text, ENT_COMPAT, 'utf-8');
    return '';
  }

  static function parseYoutube($markup) {
    return preg_replace_callback('/%%%([a-zA-Z0-9]+)%%%/i', array('Util', 'youtube'), $markup);
  }

  public static function youtube($id, $width = 300, $height = 210) {
    if (is_array($id)) $id = $id[1];
    return sprintf('<object type="application/x-shockwave-flash" width="%s" height="%s" data="https://www.youtube.com/v/%s?hl=en&amp;fs=1"><param name="wmode" value="opaque" /><param name="movie" value="https://www.youtube.com/v/%s?hl=en&amp;fs=1"/><param name="allowFullScreen" value="true"/><param name="allowscriptaccess" value="always"/></object>', $width, $height, $id, $id);
  }
}

class UtilCollectObject
{
  private $result = array();

  public function __construct($object, $relations)
  {
    $this->handle($object, $relations);
  }

  public function getResult()
  {
    return $this->result;
  }

  public function handle($object, $relations)
  {
    if (empty ($relations))
    {
      if (!isset($this->result[$object['id']])) $this->result[$object['id']] = $object;
    }
    else
    {
      $head = $relations[0];
      $tail = array_slice($relations, 1);

      if ($head[0] !== '1')
        foreach ($object[$head] as $object) $this->handle($object, $tail);
      else
        $this->handle($object[substr($head, 1)], $tail);
    }
  }

}