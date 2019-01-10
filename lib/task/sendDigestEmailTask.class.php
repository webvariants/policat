<?php
/*
 * Copyright (c) 2019, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sendDigestEmailTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace = 'policat';
    $this->name = 'send-digest-email';
    $this->briefDescription = 'Send digest e-mails';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);
    $i18n = $context->getI18N();
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $table = DigestEmailTable::getInstance();
    $con = $table->getConnection();
    $con->beginTransaction();
    $time = time();
    $i = 0;
    try {
      $query = $table
        ->createQuery('d')
        ->select('DISTINCT d.petition_id, d.contact_id, COUNT(d.id) as count, MIN(created_at) as first_at')
        ->groupBy("d.petition_id, d.contact_id")
        ->andWhere('d.status = ?', 1);
      
      $digests = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
      foreach ($digests as $digest) {
        if ($digest['count'] >= 0 || ($time - strtotime($digest['first_at'] . ' UTC')) > 3600 * 24) {
          $digest_entries = $table
            ->createQuery('d')
            ->select('d.id, d.petition_signing_id, d.tld, d.track_campaign')
            ->andWhere('d.petition_id = ?', $digest['petition_id'])
            ->andWhere('d.contact_id = ?', $digest['contact_id'])
            ->andWhere('d.status = ?', 1)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW);
          if (!$digest_entries) {
            continue;
          }
          
          $signing_ids = array_column($digest_entries, 'petition_signing_id');
          $digest_entries = array_column($digest_entries, null, 'petition_signing_id');
          
          $signings = PetitionSigningTable::getInstance()->createQuery('s')
            ->leftJoin('s.Widget w')
            ->leftJoin('w.PetitionText t')
            ->select('s.id, s.fullname, s.firstname, s.lastname, s.city, s.country, s.widget_id, w.petition_text_id, t.language_id')
            ->whereIn('s.id', $signing_ids)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW);
          
          $languages = array_count_values(array_column($signings, 'language_id'));
          arsort($languages);
          $top_language = key($languages);
          foreach ($signings as $signing) {
            if (!array_key_exists($signing['id'], $digest_entries)) {
              continue;
            }
            
            $name = $signing['lastname'] ? (($signing['firstname'] ? $signing['firstname'] . ' ' : '') . $signing['lastname']) : $signing['fullname'];
            $cols = array($name, $signing['city'], $signing['country'], $digest_entries[$signing['id']]['tld']);
            $cols = array_filter($cols);
            $digest_entries[$signing['id']]['line'] = implode(', ', $cols);
          }
          $lines = array_filter(array_column($digest_entries, 'line'));
          $petition = PetitionTable::getInstance()->findById($digest['petition_id']);
          $petition_text = PetitionTextTable::getInstance()->fetchByPetitionAndPrefLang($petition, $top_language, Doctrine_Core::HYDRATE_ARRAY);
          $subject = $petition_text['email_subject'];
          $body = $petition_text['email_body'];
          $subst_fields = $petition->getGeoSubstFields();
          $contact = ContactTable::getInstance()
            ->createQuery('c')
            ->where('c.id = ?', $digest['contact_id'])
            ->addFrom('c.ContactMeta cm')
            ->addFrom('cm.MailingListMetaChoice mlmc')
            ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
          $subst = Contact::substFieldsHelper($contact, $subst_fields);
          $i18n->setCulture($top_language);
          $subst = Contact::substFieldsSalutationHelper($contact, $i18n, $subst);
          $secret = PetitionContactTable::secretHelper($petition, $contact);
          $subst['#PLEDGE-URL#'] = $this->getRouting()->generate('pledge_contact', array(
                'petition_id' => $petition->getId(),
                'contact_id' => $contact['id'],
                'secret' => $secret
              ), true);

          $body .= "\n" . implode("\n", $lines);
          $one_entry = reset($digest_entries);
          
          try {
            UtilMail::send($one_entry['track_campaign'], 'Contact-' . $contact['id'], $petition->getFrom(), array($contact['email'] => $contact['firstname'] . ' ' . $contact['lastname']), $subject, $body, null, $subst); /* email problem */
          } catch (Swift_RfcComplianceException $e) {
            // ignore invalid emails
          }
          $i++;
        }
      }

    echo "$i digest emails sent.";
    $con->commit();
    } catch (Exception $e) {
      $con->rollback();
      print($e);
      echo 'exception in transaction.';
    }

  }

}
