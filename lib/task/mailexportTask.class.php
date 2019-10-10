<?php
/*
 * Copyright (c) 2019, webvariants GmbH, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class mailexportTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('verbose', 'v', sfCommandOption::PARAMETER_REQUIRED, 'be verbose', 0)
    ));

    $this->namespace = 'policat';
    $this->name = 'mailexport';
    $this->briefDescription = 'Export pending emails contacts to external services.';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $petitions = PetitionTable::getInstance()->queryAll()->andWhere('p.mailexport_enabled = 1')->execute();
    foreach ($petitions as $petition) {
      if ($options['verbose']) {
        echo "export petition: " . $petition->getId() .  "\n";
      }

      foreach (MailExport::getServices() as $service) {
        if ($service->checkEnabled($petition)) {
          if ($options['verbose']) {
            echo "service: " . $service->getName() .  "\n";
          }

          $result = $service->export($petition, !!$options['verbose']);
          if ($result['status']) {
            if ($options['verbose']) {
              echo $result['message'] . "\n";
            }

            if ($result['ids']) {
              $query = PetitionSigningTable::getInstance()->createQuery()->update('PetitionSigning ps');
              $query->whereIn('id', $result['ids']);
              $query->set('mailexport_pending', PetitionSigning::MAILEXPORT_PENDING_DONE);
              $query->execute();
            }
          } else {
            if ($options['verbose']) {
              echo "error: " . $result['message'] .  "\n";
            }
            $ticket = TicketTable::getInstance()->generate(array(
              TicketTable::CREATE_PETITION => $petition,
              TicketTable::CREATE_KIND => TicketTable::KIND_MAILEXPORT_ERROR,
              TicketTable::CREATE_CHECK_DUPLICATE => true,
              TicketTable::CREATE_TEXT => $result['message'],
              TicketTable::CREATE_TO => $petition->getCampaign()->getDataOwner(),
            ));
            if ($ticket) {
              $ticket->save();
              $ticket->notifyAdmin();
              if ($options['verbose']) {
                echo "ticket created\n";
              }
            }
          }
          break;
        }
      }
    }

  }

}
