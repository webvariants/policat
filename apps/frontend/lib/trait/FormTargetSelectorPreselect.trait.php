<?php

trait FormTargetSelectorPreselect {

  private function configureTargetSelectors($petition) {
    if ($petition->getKind() == Petition::KIND_EMAIL_TO_LIST || $petition->getKind() == Petition::KIND_PLEDGE) {
      if ($petition->getMailingListId()) {
//        $mailinglist = $petition->getMailingList();
//        /* @var $mailinglist MailingList */
        $target_selectors = $petition->getTargetSelectors();

        if ($target_selectors) {
          $first = $target_selectors[0];
          if ($first['id'] === MailingList::FIX_COUNTRY || $first['id'] === 'contact' || is_numeric($first['id'])) {
            $choices = array('' => '') + $first['choices'];

            $this->setWidget('target_selector_1', new sfWidgetFormChoice(array('choices' => $choices, 'label' => $first['name'])));
            $this->setValidator('target_selector_1', new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false)));

            if (count($target_selectors) === 2) {
              $second = $target_selectors[1];
              $second_choices = array('' => '');
              $old = $this->decodeTargetSelector();
              if ($old && $old['selector_1']['value']) {
                $this->setDefault('target_selector_1', $old['selector_1']['value']);
                $second_choices = $petition->getTargetSelectorChoices($old['selector_1']['value']);
                if (is_array($second_choices)) {
                  $second_choices = array('' => '') + $second_choices['choices'];
                }
              }

              $this->setWidget('target_selector_2', new sfWidgetFormChoice(array('choices' => $second_choices, 'label' => $second['name'])));
              $this->setValidator('target_selector_2', new sfValidatorChoice(array('choices' => array_keys($second_choices), 'required' => false)));

              if ($old && $old['selector_2']['value']) {
                $this->setDefault('target_selector_2', $old['selector_2']['value']);
              }

              $this->getWidget('target_selector_1')->setAttribute('class', 'ajax_change post');
              $this->getWidget('target_selector_1')->setAttribute('data-action', sfContext::getInstance()->getRouting()->generate('target_choices_petition', array('id' => $petition->getId())));
            }
          }
        }
      }
    }
  }

  private function decodeTargetSelector() {
    $json = $this->getObject()->getEmailTargets();
    if ($json) {
      return json_decode($json, true);
    }

    return array();
  }

  private function processTargetSelectorValues($values) {
    $email_targets = null;

    $petition = $this->getObject()->getPetition();
    if ($petition->getKind() == Petition::KIND_EMAIL_TO_LIST || $petition->getKind() == Petition::KIND_PLEDGE) {
      if ($petition->getMailingListId()) {
        $target_selectors = $petition->getTargetSelectors();
        if ($target_selectors) {
          $first = $target_selectors[0];
          if (array_key_exists('target_selector_1', $values)) {
            $target_selector_1 = $values['target_selector_1'];
            unset($values['target_selector_1']);

            if ($target_selector_1) {
              $data = array(
                  'mailing_list_id' => $petition->getMailingListId(),
                  'selector_1' => array(
                      'value' => $target_selector_1,
                      'id' => $first['id'],
                      'kind' => array_key_exists('kind', $first) ? $first['kind'] : null,
                      'mapping_id' => array_key_exists('mapping_id', $first) ? $first['mapping_id'] : null,
                      'meta_id' => array_key_exists('meta_id', $first) ? $first['meta_id'] : null
                  ),
                  'selector_2' => array(
                      'value' => null,
                      'id' => null,
                      'kind' => null,
                      'mapping_id' => null,
                      'meta_id' => null
                  )
              );

              if (count($target_selectors) === 2) {
                $second = $target_selectors[1];

                if (array_key_exists('target_selector_2', $values)) {
                  $target_selector_2 = $values['target_selector_2'];
                  unset($values['target_selector_2']);

                  if ($target_selector_2) {
                    $data['selector_2'] = array(
                        'value' => $target_selector_2,
                        'id' => $second['id'],
                        'kind' => array_key_exists('kind', $second) ? $second['kind'] : null,
                        'mapping_id' => array_key_exists('mapping_id', $second) ? $second['mapping_id'] : null,
                        'meta_id' => array_key_exists('meta_id', $second) ? $second['meta_id'] : null
                    );
                  }
                }
              }
            }

            $email_targets = json_encode($data);
          }
        }
      }
    }
    $values['email_targets'] = $email_targets;

    return $values;
  }

}
