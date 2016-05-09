<?php

class ContactUploadStep2Form extends sfForm {

  protected $first_row = array();

  public function setup() {
    parent::setup();

    $select = array();
    $select_key = array();

    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('contact_upload2[%s]');

    $this->setWidget('separator', new sfWidgetFormInputHidden());
    $this->setValidator('separator', new sfValidatorString(array('min_length' => 1, 'max_length' => 1)));
//    $this->getWidgetSchema()->setDefault('separator', ',');

    $this->setWidget('separator2', new sfWidgetFormInputText());
    $this->setValidator('separator2', new sfValidatorString(array('min_length' => 1, 'max_length' => 1)));
    $this->getWidgetSchema()->setDefault('separator2', '|');
    $this->getWidgetSchema()->setLabel('separator2', 'Separator inside fields');

    $this->setWidget('female', new sfWidgetFormInputText());
    $this->setValidator('female', new sfValidatorString(array('min_length' => 1)));
    $this->getWidgetSchema()->setDefault('female', 'female');

    $this->setWidget('male', new sfWidgetFormInputText());
    $this->setValidator('male', new sfValidatorString(array('min_length' => 1)));
    $this->getWidgetSchema()->setDefault('male', 'male');

    $ml = $this->getOption('MailingList');
    if ($ml instanceof MailingList) {
      $substs = $ml->getSubstFields();
      ksort($substs);
      foreach ($substs as $subst) {
        $this->setWidget('field_' . $subst['id'], new sfWidgetFormChoice(array('choices' => $select)));
        $this->setValidator('field_' . $subst['id'], new sfValidatorChoice(array('choices' => $select_key)));
        $this->getWidgetSchema()->setLabel('field_' . $subst['id'], $subst['name']);
      }
    }
    $this->getWidgetSchema()->setHelp('field_country', 'Must be encoded with 2 letter ISO code.');

    $this->setWidget('language', new sfWidgetFormChoice(array('label' => 'Language', 'choices' => $select)));
    $this->setValidator('language', new sfValidatorChoice(array('choices' => $select_key, 'required' => false)));
    $this->getWidgetSchema()->setHelp('language', 'The following ISO codes are allowed: ' . implode(', ', LanguageTable::getInstance()->fetchLanguageIds()));

    $this->setWidget('file', new sfWidgetFormInputHidden());
    $this->setValidator('file', new sfValidatorRegex(array('pattern' => ContactUploadStep1Form::FILE_PATTERN)));
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null) {
    $ok = $this->afterBind($taintedValues);
    parent::bind($taintedValues, $taintedFiles);
    return $ok;
  }

  public function afterBind($params) {
    $filename = (is_array($params) && array_key_exists('file', $params)) ? $params['file'] : null;
    if (!is_string($filename) || !preg_match(ContactUploadStep1Form::FILE_PATTERN, $filename)) {
      return false;
    }

    $separator = (is_array($params) && array_key_exists('separator', $params)) ? $params['separator'] : null;
    if (!is_string($separator) || strlen($separator) !== 1) {
      return false;
    }

    $this->setSeparator($separator);
    $this->setFile($filename);

    return true;
  }

  public function setFile($filename, $autoselect = false) {
    $this->setDefault('file', $filename);
    $pathfile = ContactUploadStep1Form::getDir($filename);
    $data = false;

    if (($handle = @fopen($pathfile, "r")) !== false) {
      setlocale(LC_ALL, 'en_US.UTF-8'); // fixes missing Umlauts
      $data = fgetcsv($handle, 0, $this->getDefault('separator'));
      if ($data !== false) {
        $this->setFirstRow($data, $autoselect);
      }

      @fclose($handle);
    }

    return $data;
  }

  public function setSeparator($separator) {
    $this->setDefault('separator', $separator);
  }

  protected function setFirstRow($first_row, $autoselect = false) {
    $choices = array('' => 'select');
    $choices_flip = array();

    $names = array();
    $ml = $this->getOption('MailingList');
    if ($ml instanceof MailingList) {
      $substs = $ml->getSubstFields();
      foreach ($substs as $ksubst => $subst) {
        $names[$subst['id']] = array(strtolower($ksubst), strtolower($subst['name']));
        if (!is_numeric($subst['id'])) {
          $names[$subst['id']][] = strtolower($subst['id']);
        }
      }
    }

    foreach ($first_row as $key => $value) {
      $choices[$key] = ($key + 1) . '. ' . $value;
      $choices_flip[strtolower($value)] = $key;
    }
    foreach ($this->getWidgetSchema()->getFields() as $key => $widget) {
      if ($widget instanceof sfWidgetFormChoice && ($key == 'language' || strpos($key, 'field_') === 0)) {
        $widget->setOption('choices', $choices);

        if ($autoselect) {
          if (strpos($key, 'lang') === 0) {
            if (array_key_exists($key, $choices_flip)) {
              $this->setDefault($key, $choices_flip[$key]);
            }
          } elseif (strpos($key, 'field_') === 0) {
            $field_key = substr($key, 6);
            if (array_key_exists($field_key, $names)) {
              foreach ($names[$field_key] as $name) {
                if (array_key_exists($name, $choices_flip)) {
                  $this->setDefault($key, $choices_flip[$name]);
                }
              }
            }
          }
        }
      }
    }

    $choices_keys = array_keys($choices);

    foreach ($this->getValidatorSchema()->getFields() as $key => $validator) {
      if ($validator instanceof sfValidatorChoice && ($key == 'language' || strpos($key, 'field_') === 0)) {
        $validator->setOption('choices', $choices_keys);
      }
    }
  }

  public function save() {
    $separator = $this->getValue('separator');
    $separator2 = $this->getValue('separator2');
    $ml = $this->getOption('MailingList');
    if ($ml instanceof MailingList) {
      $con = $ml->getTable()->getConnection();
      $con->beginTransaction();
      try {
        $ml->invalidateCache();
        $substs = $ml->getSubstFields();
        ksort($substs);

        $meta_choices_db = Doctrine_Core::getTable('MailingListMeta')
          ->createQuery('mlm')
          ->where('mlm.mailing_list_id = ?', $ml->getId())
          ->andWhere('mlm.kind = ?', MailingListMeta::KIND_CHOICE)
          ->addFrom('mlm.MailingListMetaChoice mlmc')
          ->fetchArray();
        $meta_choices = array();
        foreach ($meta_choices_db as $meta_choice_db) {
          $choices = array();
          foreach ($meta_choice_db['MailingListMetaChoice'] as $choice_db) {
            $choices[mb_strtolower(trim($choice_db['choice']), 'utf-8')] = $choice_db['id'];
          }
          $meta_choices[$meta_choice_db['id']] = $choices;
        }

        if (($handle = @fopen(ContactUploadStep1Form::getDir($this->getValue('file')), 'r')) !== false) {
          $skip = true;
          $female = $this->getValue('female');
          $male = $this->getValue('male');

          $countries = array_keys(sfCultureInfo::getInstance()->getCountries());
          $language_col = $this->getValue('language');
          if (strlen($language_col)) {
            $language_ids = LanguageTable::getInstance()->fetchLanguageIds();
          }

          setlocale(LC_ALL, 'en_US.UTF-8'); // fixes missing Umlauts
          while (($data = fgetcsv($handle, 0, $separator)) !== false) {
            if ($skip) {
              $skip = false;
              continue;
            }

            $contact = new Contact();
            $contact->setMailingListId($ml->getId());
            foreach ($substs as $subst) {
              $value = '';
              $col = (int) ($this->getValue('field_' . $subst['id']));
              if (array_key_exists($col, $data))
                $value = trim($data[$col]);

              switch ($subst['type']) {
                case 'fix':
                  switch ($subst['id']) {
                    case 'gender':
                      $contact['gender'] = $value == $female ? Contact::GENDER_FEMALE : ($value == $male ? Contact::GENDER_MALE : Contact::GENDER_NEUTRAL);
                      break;
                    case 'country':
                      $country = strtoupper($value);
                      if (in_array($country, $countries))
                        $contact['country'] = $country;
                      break;
                    default:
                      $contact[$subst['id']] = $value;
                  }
                  break;
                case 'free':
                  $meta = new ContactMeta();
                  $meta->setMailingListMetaId($subst['id']);
                  $meta->setValue($value);
                  $contact->ContactMeta[] = $meta;
                  break;
                case 'choice':
                  if (array_key_exists($subst['id'], $meta_choices)) {
                    $choices = $meta_choices[$subst['id']];
                    if ($subst['multi']) {
                      foreach (explode($separator2, mb_strtolower($value, 'utf-8')) as $value_i) {
                        $lower = trim($value_i);
                        if (array_key_exists($lower, $choices)) {
                          $meta = new ContactMeta();
                          $meta->setMailingListMetaId($subst['id']);
                          $meta->setMailingListMetaChoiceId($choices[$lower]);
                          $contact->ContactMeta[] = $meta;
                        }
                      }
                    } else {
                      $lower = mb_strtolower($value, 'utf-8');
                      if (array_key_exists($lower, $choices)) {
                        $meta = new ContactMeta();
                        $meta->setMailingListMetaId($subst['id']);
                        $meta->setMailingListMetaChoiceId($choices[$lower]);
                        $contact->ContactMeta[] = $meta;
                      }
                    }
                  }
                  break;
              }
            }

            if (strlen($language_col)) {
              if (array_key_exists((int) $language_col, $data)) {
                $language_id = trim($data[(int) $language_col]);

                if (in_array($language_id, $language_ids)) {
                  $contact->setLanguageId($language_id);
                }
              }
            }

            $contact->save();
          }
          @fclose($handle);
        }
        $con->commit();
      } catch (Exception $e) {
        $con->rollback();
        return false;
      }
    }
    return true;
  }

}
