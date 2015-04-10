<?php

/**
 * pledge_contact actions.
 *
 * @package    policat
 * @subpackage pledge_contact
 * @author     Martin
 */
class pledge_contactActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $petiion_id = $request->getParameter('petition_id');
    $contact_id = $request->getParameter('contact_id');
    $this->show_thankyou = false;

    if ($contact_id) {
      $petition_contact = PetitionContactTable::getInstance()->findOneByPetitionIdAndContactId($petiion_id, $contact_id);
      if (!$petition_contact) {
        return $this->notFound();
      }

      if ($petition_contact->getSecret() != $request->getParameter('secret')) {
        return $this->notFound();
      }

      $contact = $petition_contact->getContact();
      $petition = $petition_contact->getPetition();

      /* @var $petition Petition */
    } else {
      $petition = PetitionTable::getInstance()->find($petiion_id);
      if (!$petition) {
        return $this->notFound();
      }

      $contact = new Contact();
      $contact->setFirstname('John');
      $contact->setLastname('Doe');
      $contact->setGender(Contact::GENDER_MALE);

      $petition_contact = new PetitionContact();
      $petition_contact->setPetition($petition);
      $petition_contact->setContact($contact);
      $this->show_thankyou = true;
    }

    $languages = LanguageTable::getInstance()->queryByActivePetitionTexts($petition)->execute();
    $this->languages = $languages;
    $language_ids = array();
    foreach ($languages as $language) {
      $language_ids[] = $language->getId();
    }


    $contact_lang = $contact->getLanguageId() ? : 'en';
    if (!in_array($contact_lang, $language_ids)) {
      $contact_lang = in_array('en', $language_ids) ? 'en' : reset($language_ids);
    }
    $lang = $request->getGetParameter('lang');
    if ($lang && in_array($lang, $language_ids)) {
      $contact_lang = $lang;
    }
    $contact->setLanguageId($contact_lang);

    $petition_text = $contact->getPetitionTextForPetition($petition);
    $this->getUser()->setCulture($contact_lang);

    if (!$petition_text) {
      return $this->notFound();
    }

    /* @var $petition_text PetitionText */

    $i18n = $this->getContext()->getI18N();
    $i18n->setCulture($petition_text->getLanguageId());

    $salutation = $contact->generateSalutation($i18n);

    $this->salutation = $salutation;
    $this->petition_text = $petition_text;
    $this->petition = $petition;
    $this->petition_contact = $petition_contact;
    $this->ask_password = false;
    $this->wrong_password = false;
    $this->session = null;
    $this->password_no_match = false;
    $this->password_too_short = false;

    if ($petition_contact->getPassword()) {
      $session = $request->getPostParameter('session');

      if ($session && is_string($session) && $session == crypt($petition_contact->getPassword(), $session)) {
        $this->session = $session;
      } else {
        if ($request->isMethod('post')) {
          $password = trim($request->getPostParameter('password'));
          if ($password) {
            if ($petition_contact->checkPassword($password)) {
              $this->session = crypt($petition_contact->getPassword(), '$6$' . PetitionContactTable::salt());
            } else {
              $this->wrong_password = true;
              $this->ask_password = true;
              return;
            }
          } else {
            $this->ask_password = true;
            return;
          }
        } else {
          $this->ask_password = true;
          return;
        }
      }
    }

    $pledge_table = PledgeTable::getInstance();
    $pledge_items = $petition->getPledgeItems();
    $pledges = array();
    foreach ($pledge_items as $pledge_item) {
      /* @var $pledge_item PledgeItem */
      if ($pledge_item->getStatus() == PledgeItemTable::STATUS_ACTIVE) {
        $pledge = $pledge_table->findOneByPledgeItemAndContact($pledge_item, $contact);
        if (!$pledge) {
          $pledge = new Pledge();
          $pledge->setPledgeItem($pledge_item);
          $pledge->setContact($contact);
          if (!$contact->isNew()) {
            $pledge->save();
          }
        } else {
          $pledge->setPledgeItem($pledge_item);
        }

        $pledges[] = $pledge;
      }
    }

    if ($request->isMethod('post')) {
      $this->show_thankyou = true;
      $pledge_changed = false;
      foreach ($pledges as $pledge) {
        $status = $request->getPostParameter('status_' . $pledge->getPledgeItem()->getId());

        if (in_array($status, array(PledgeTable::STATUS_YES, PledgeTable::STATUS_NO, PledgeTable::STATUS_NO_COMMENT))) {
          $pledge_changed = $pledge_changed || $pledge->getStatus() != $status;

          if ($pledge->getStatus() != PledgeTable::STATUS_YES) {
            if ($pledge->getStatus() != $status) {
              $pledge->setStatusAt(gmdate('Y-m-d H:i:s'));
            }
            $pledge->setStatus($status);
          }
        }

        $pledge->save();
      }
      if ($petition->getPledgeWithComments()) {
        $comment = $request->getPostParameter('comment');
        if (is_string($comment)) {
          $petition_contact->setComment(trim($comment));
          $petition_contact->save();
        }
      }

      if ($pledge_changed) {
        $petition->state(Doctrine_Record::STATE_DIRTY); // trigger widget update
        $petition->save();
      }

      $password1 = trim($request->getPostParameter('new_password1'));
      $password2 = trim($request->getPostParameter('new_password2'));
      if ($password1) {
        if ($password1 !== $password2) {
          $this->password_no_match = true;
          $this->show_thankyou = false;
        } else if (strlen($password1) < 8) {
          $this->password_too_short = true;
          $this->show_thankyou = false;
        } else {
          $petition_contact->setHashPassword($password1);
          $petition_contact->save();
          $this->session = crypt($petition_contact->getPassword(), '$6$' . PetitionContactTable::salt());
        }
      }
    }
    $this->pledges = $pledges;
  }

}
