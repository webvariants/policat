<?php

class UtilTargetSelectorPreselect {

  public static function staticTargetSelectors(Widget $widget) {
    $petition = $widget->getPetition();

    $target_selectors = $petition->getTargetSelectors();
    if (!$target_selectors) {
      return false;
    }

    $preselected = self::decodeTargetSelectors($widget->getEmailTargets(), $petition->getMailingListId(), $target_selectors);
    if (!$preselected) {
      self::decodeTargetSelectors($widget->getPetitionText()->getEmailTargets(), $petition->getMailingListId(), $target_selectors);
    }

    if (!$preselected) {
      return $target_selectors;
    }

    $num_fix = 0;

    if ($preselected['selector_1']['value']) {
      $target_selectors[0]['fixed'] = $preselected['selector_1']['value'];
      $num_fix = 1;

      if ($preselected['selector_2']['value']) {
        $target_selectors[1]['fixed'] = $preselected['selector_2']['value'];
        $num_fix = 2;
      }
    }

    switch ($num_fix) {
      case 1:
        $choices_1 = $petition->getTargetSelectorChoices($target_selectors[0]['fixed']);
        if (count($target_selectors) >= 2) {
          if ($petition->getKind() == Petition::KIND_PLEDGE) {
            $target_selectors[1]['fix_choices_plegde'] = $choices_1['choices'];
            $target_selectors[1]['fix_choices_plegde_all'] = $petition->getTargetSelectorChoices2($target_selectors[0]['fixed'], 'aöö');
          } else {
            $target_selectors[1]['choices'] = $choices_1['choices'];
          }
        } else {
          if ($petition->getKind() == Petition::KIND_PLEDGE) {
            $target_selectors[0]['choices'] = $choices_1['choices'];
          } else {
            $target_selectors[0]['fix_choices'] = $choices_1['choices'];
          }
        }
        if ($petition->getKind() == Petition::KIND_PLEDGE) {
          $target_selectors[0]['pledges'] = $choices_1['pledges'];
          $target_selectors[0]['infos'] = $choices_1['infos'];
        }
        break;
      case 2:
        $choices_2 = $petition->getTargetSelectorChoices2($target_selectors[0]['fixed'], $target_selectors[1]['fixed']);
        if ($petition->getKind() == Petition::KIND_PLEDGE) {
          $target_selectors[1]['choices'] = $choices_2['choices'];
        } else {
          $target_selectors[1]['fix_choices'] = $choices_2['choices'];
        }
        $target_selectors[1]['fix_label'] = sfContext::getInstance()->getI18N()->__('Recipient(s)');
        if ($petition->getKind() == Petition::KIND_PLEDGE) {
          $target_selectors[1]['pledges'] = $choices_2['pledges'];
          $target_selectors[1]['infos'] = $choices_2['infos'];
        }
        break;
    }

    return $target_selectors;
  }

  public static function decodeTargetSelectors($json, $mailing_list_id, $target_selectors) {
    if (!$json) {
      return array();
    }

    $data = json_decode($json, true);
    if (!$data || !is_array($data)) {
      return array();
    }
    if ($data['mailing_list_id'] != $mailing_list_id) {
      return array();
    }

    $first = count($target_selectors) > 0 ? $target_selectors[0] : null;
    $second = count($target_selectors) > 1 ? $target_selectors[1] : null;

    if (!self::matchTargetSelector($first, $data['selector_1'])) {
      $data['selector_1'] = $data['selector_2'] = array(
          'value' => null,
          'id' => null,
          'kind' => null,
          'mapping_id' => null,
          'meta_id' => null
      );
    } elseif (!self::matchTargetSelector($second, $data['selector_2'])) {
      $data['selector_2'] = array(
          'value' => null,
          'id' => null,
          'kind' => null,
          'mapping_id' => null,
          'meta_id' => null
      );
    }

    if ($data['selector_1'] === null && $data['selector_2'] === null) {
      return array();
    }

    return $data;
  }

  private static function matchTargetSelector($selector, $data) {
    return $selector && array_key_exists('id', $data) &&
      $selector['id'] == $data['id'] &&
      (array_key_exists('kind', $selector) ? $selector['kind'] : null) == $data['kind'] &&
      (array_key_exists('mapping_id', $selector) ? $selector['mapping_id'] : null) == $data['mapping_id'] &&
      (array_key_exists('meta_id', $selector) ? $selector['meta_id'] : null) == $data['meta_id'];
  }

  public static function printTextPreselection(PetitionText $petition_text, $printf_format = '%s', $printf_format_selector = '<strong>%s: </strong>%s<br />') {
    $mailing_list_id = $petition_text->getPetition()->getMailingListId();
    if (!$mailing_list_id) {
      return;
    }
    $target_selectors = $petition_text->getPetition()->getTargetSelectors();
    if (!$target_selectors) {
      return;
    }
    $json = $petition_text->getEmailTargets();
    $preselects = self::decodeTargetSelectors($json, $mailing_list_id, $target_selectors);

    if (!$preselects) {
      return;
    }

    $ret = '';

    foreach (array(0, 1) as $i) {
      $selector = $preselects['selector_' . ($i + 1)];
      if ($selector['value']) {
        $value = $selector['value'];
        if ($value) {
          $choice = '';
          if (is_numeric($target_selectors[$i]['id'])) {
            $choice = MailingListMetaChoiceTable::getInstance()->findByMailingListMetaIdAndId($mailing_list_id, $value);
            /* @var $choice MailingListMetaChoice */

            $choice = $choice ? $choice->getChoice() : $value;
          } elseif ($target_selectors[$i]['id'] === 'country') {
            try {
            $country_name = sfCultureInfo::getInstance('en')->getCountries(array($value));
            $choice = $country_name[$value];
            } catch (Exception $e) {
              $choice = $value;
            }
          } else {
            $choice = 'please write a bugreport';
          }

          $ret .= sprintf($printf_format_selector, Util::enc($target_selectors[$i]['name']), Util::enc($choice));
        } else {
          break;
        }
      }
    }

    if ($ret) {
      printf($printf_format, $ret);
    }
  }

}
