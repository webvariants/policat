<?php
class UtilWidgetTree
{
  public static function printUL($widgets, $children, $ids, $link = false)
  {
    echo "<ul>";
    foreach ($ids as $id) {
      echo "<li>";
      printf ("Organisation: <b>%s</b> %s<br/>Email: <b>%s</b> %s<br/>Created on page: <b>%s</b><br/>",
        htmlentities($widgets[$id]['organisation'], ENT_COMPAT, 'utf-8'),
        $link ? strtr($link, array(
          'CID' => $widgets[$id]['campaign_id'],
          'WID' => $widgets[$id]['id'],
          'HASH' => $widgets[$id]->getLastHash()
          )) : '',
        htmlentities($widgets[$id]['email'], ENT_COMPAT, 'utf-8'),
        $widgets[$id]['validation_status'] == Widget::VALIDATION_STATUS_VERIFIED ? '<b>(verified)</b> ' : '',
        htmlentities($widgets[$id]['ref'], ENT_COMPAT, 'utf-8')
        );
      if (!empty($children[$id])) self::printUL($widgets, $children, $children[$id], $link);
      echo "</li>";
    }
    echo "</ul>";
  }
}