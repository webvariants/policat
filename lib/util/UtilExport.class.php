<?php

class UtilExport {

  const PAGE_SIZE = 1000;

  public static $EXCLUDE = array(
      false => array(
          Petition::FIELD_EMAIL,
          Petition::FIELD_ADDRESS,
//          Petition::FIELD_COMMENT,
          Petition::FIELD_EMAIL_SUBJECT,
          Petition::FIELD_EMAIL_BODY,
          Petition::FIELD_PRIVACY
      ),
      true => array(
          Petition::FIELD_SUBSCRIBE,
          Petition::FIELD_COMMENT,
          Petition::FIELD_EMAIL_SUBJECT,
          Petition::FIELD_EMAIL_BODY,
          Petition::FIELD_PRIVACY
      )
  );

  public static function pages($count) {
    return ceil($count / self::PAGE_SIZE);
  }

  public static function writeCsv($filename, Doctrine_Query $query, $subscribers, $page = 0) {
    $out = fopen($filename, 'a+');
    $formfields = array_keys(Petition::$FIELD_SHOW); // $petition->getFormfields();
    $exclude = self::$EXCLUDE[$subscribers];
    $used_fields = array(
        'created_at' => 0,
        'updated_at' => 1,
        'status' => 2,
    );
    $blank = array('', '', '');
    $widget_id_to_language_id = array();

    ini_set('max_execution_time', 600);
    set_time_limit(120);

    $query_i = $query->copy();
    $petition_signings = $query_i
      ->select('ps.*, w.id, w.petition_text_id, pt.id, pt.language_id')
      ->offset(self::PAGE_SIZE * $page)
      ->limit(self::PAGE_SIZE)
      ->execute();
    $i = 0;
    foreach ($petition_signings as $petition_signing) { /* @var $petition_signing PetitionSigning */
      $widget_id = $petition_signing->getWidgetId();
      $cell = $blank;
      $cell[0] = $petition_signing->getCreatedAt();
      $cell[1] = $petition_signing->getUpdatedAt();
      $cell[2] = $petition_signing->getStatusName();
      foreach ($formfields as $formfield) {
        if (!in_array($formfield, $exclude)) {
          $value = $petition_signing->getField($formfield);
          if ($value !== null) {
            if (!array_key_exists($formfield, $used_fields)) {
              $used_fields[$formfield] = count($used_fields);
              $blank[] = '';
            }
            $cell[$used_fields[$formfield]] = $value;
          }
        }
      }
      if (!array_key_exists(Petition::FIELD_REF, $used_fields)) {
        $used_fields[Petition::FIELD_REF] = count($used_fields);
        $blank[] = '';
        $used_fields['widget_id'] = count($used_fields);
        $blank[] = '';
        $used_fields['language_id'] = count($used_fields);
        $blank[] = '';
        if (!$subscribers) {
          $used_fields['hash'] = count($used_fields);
          $blank[] = '';
        }
      }
      $cell[$used_fields[Petition::FIELD_REF]] = $petition_signing->getField(Petition::FIELD_REF);
      $cell[$used_fields['widget_id']] = $widget_id;
      $language_id = '';
      if ($widget_id) {
        if (!array_key_exists($widget_id, $widget_id_to_language_id)) {
          $widget_id_to_language_id[$widget_id] = $petition_signing->getWidget()->getPetitionText()->getLanguageId();
        }

        $language_id = $widget_id_to_language_id[$petition_signing->getWidgetId()];
      }
      $cell[$used_fields['language_id']] = $language_id;
      if (!$subscribers) {
        $cell[$used_fields['hash']] = $petition_signing->getEmailHashAuto();
      }

      if ($i === 0 && $page === 0) {
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, array_keys($used_fields), ';');
      }

      fputcsv($out, $cell, ';');
      $i++;
    }

    $petition_signings->free();
    unset($petition_signings);
    $query_i->free();
    unset($query_i);

    fclose($out);
  }

}
