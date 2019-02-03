<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class geoCronTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace = 'policat';
    $this->name = 'geo-cron';
    $this->briefDescription = 'Send e-mails from Geo Activism';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);
    $i18n = $context->getI18N();
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $petition_text_by_lang = array();
    $table = PetitionSigningTable::getInstance();
    $con = $table->getConnection();
    $con->beginTransaction();
    try {
      $petition_signing = $table
        ->createQuery('ps')
        ->addFrom('ps.PetitionSigningWave psw')
        ->where('ps.wave_cron > 0')
        ->andWhere('DATE_SUB(NOW(),INTERVAL 2 MINUTE) > ps.updated_at') // just to have the creating transaction ready
        ->andWhere('psw.status = ?', PetitionSigning::STATUS_COUNTED)
        ->orderBy('ps.created_at DESC')
        ->fetchOne();
      ;
      if (empty($petition_signing)) {
        echo "nothing pending. (stop)";
        $connection->rollback();
        return;
      }

      /* @var $petition_signing PetitionSigning */
      $petition = $petition_signing->getPetition();
      /* @var $petition Petition */
      $is_pledge = $petition->getKind() == Petition::KIND_PLEDGE;

      $waves_verified = array();
      foreach ($petition_signing->getPetitionSigningWave() as $wave) {
        if ($wave->getStatus() == PetitionSigning::STATUS_COUNTED) {
          $waves_verified[] = $wave->getWave();
          $wave->setStatus(PetitionSigning::STATUS_SENT);
        }
      }

      $contacts = ContactTable::getInstance()
        ->createQuery('c')
        ->addFrom('c.PetitionSigningContact psc')
        ->where('psc.petition_signing_id = ?', $petition_signing->getId())
        ->andWhereIn('psc.wave', $waves_verified)
        ->addFrom('c.ContactMeta cm')
        ->addFrom('cm.MailingListMetaChoice mlmc')
        ->fetchArray();
      $subst_fields = $petition_signing->getPetition()->getGeoSubstFields();
      $i = 0;
      foreach ($contacts as $contact) {
        if ($contact['bounce']) {
          continue;
        }

        $subst = $petition_signing->getSubst();
        $subst = Contact::substFieldsHelper($contact, $subst_fields, $subst);
        $wave = $petition_signing->getWave($contact['PetitionSigningContact'][0]['wave']);
        /** @var PetitionSigningWave $wave */
        $wave_lang_id = $wave->getLanguageId();
        if ($wave_lang_id) {
          $i18n->setCulture($wave_lang_id);
        }
        $subst = Contact::substFieldsSalutationHelper($contact, $i18n, $subst);

        if ($wave) {
          if ($is_pledge) {
            $secret = PetitionContactTable::secretHelper($petition, $contact);
            $subst['#PLEDGE-URL#'] = $this->getRouting()->generate('pledge_contact', array(
                'petition_id' => $petition->getId(),
                'contact_id' => $contact['id'],
                'secret' => $secret
              ), true);
          }

          if (array_key_exists($contact['language_id'], $petition_text_by_lang)) {
            $petition_text = $petition_text_by_lang[$contact['language_id']];
          } else {
            $petition_text = PetitionTextTable::getInstance()->fetchByPetitionAndPrefLang($petition, $contact['language_id'], Doctrine_Core::HYDRATE_ARRAY);
            $petition_text_by_lang[$contact['language_id']] = $petition_text;
          }

          if ($is_pledge) {

            if ($petition_text) {
              $subject = $petition_text['email_subject'];
              $body = $petition_text['email_body'];
            } else {
              $subject = $body = '';
            }
          } else {
            $subject = $wave->getField(Petition::FIELD_EMAIL_SUBJECT);
            $body = $wave->getField(Petition::FIELD_EMAIL_BODY);
          }

          if ($petition_text) {
            UtilMail::appendMissingKeywords($body, $petition_text['email_body'], PetitionSigningTable::$KEYWORDS);
          }

          $i++;
          if (!$is_pledge || !$petition->getDigestEnabled()) {
            try {
              /* Email to target  */
              if ($is_pledge) {
                UtilMail::send('Email-To-List-' . $petition->getCampaignId(), 'Contact-' . $contact['id'], $wave->getEmailContact($petition->getFromEmail(), true), array($contact['email'] => $contact['firstname'] . ' ' . $contact['lastname']), $subject, $body, null, null, $subst, $wave->getEmailContact(), array(), true); /* email problem */
              } else {
                UtilMail::send('Email-To-List-' . $petition->getCampaignId(), 'Contact-' . $contact['id'], $wave->getEmailContact($petition->getFromEmail(), true), array($contact['email'] => $contact['firstname'] . ' ' . $contact['lastname']), $subject, $body, null, $subst, null, $wave->getEmailContact()); /* email problem */
              }
            } catch (Swift_RfcComplianceException $e) {
              // ignore invalid emails
            }
          } else {
            $digest_email = new DigestEmail();
            $digest_email->setContactId($contact['id']);
            $digest_email->setPetitionSigningId($petition_signing->getId());
            $digest_email->setPetitionId($petition->getId());
            $digest_email->setTrackCampaign('Email-To-List-' . $petition->getCampaignId());
            $digest_email->setTld($wave->getRefTld());
            // $digest_email->setEmailSubject($subject);
            // $digest_email->setEmailBody($body);
            // $digest_email->setSubstJson(json_encode($subst));
            $digest_email->save();
          }
        }
      }
      $id = $petition_signing->getId();
      $waves_sent = implode(',', $waves_verified);
      $petition_signing->setWaveSent($petition_signing->getWaveCron());
      $petition_signing->setWaveCron(0);

      if ($i > 0 & StoreTable::value(StoreTable::BILLING_ENABLE)) {
        $campaign = $petition_signing->getPetition()->getCampaign();
        if ($campaign->getBillingEnabled() && $campaign->getQuotaId()) {
          QuotaTable::getInstance()->useQuota($campaign->getQuotaId(), $i);
          $petition_signing->setQuotaId($campaign->getQuotaId());
          $petition_signing->setQuotaEmails($i);
        }
      }

      $petition_signing->save();

      echo "$i mails sent. [$id:$waves_sent] (continue)";
      $con->commit();
    } catch (Exception $e) {
      $con->rollback();
      print($e);
      echo 'exception in transaction. (stop)';
    }
  }

}
