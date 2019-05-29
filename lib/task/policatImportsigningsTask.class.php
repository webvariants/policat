<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatImportsigningsTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('petition_id', sfCommandArgument::REQUIRED, 'the petition id'),
      new sfCommandArgument('filename', sfCommandArgument::REQUIRED, 'filename (csv)'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'policat';
    $this->name             = 'import-signings';
    $this->briefDescription = 'Import Signings from file';
    $this->detailedDescription = <<<EOF
The [policat:import-signings|INFO] task does things.
Call it with:

  [php symfony policat:import-signings|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $filename = $arguments['filename'];
    $petition_id = $arguments['petition_id'];

    $search_table = PetitionSigningSearchTable::getInstance();
    $petition = PetitionTable::getInstance()->findById($petition_id, true);
    if (empty ($petition))
    {
      echo "Petition not found.\n";
      return;
    }
    $campaign = $petition->getCampaign();
    $formfields = $petition->getFormfields();
    $formfields[] = Petition::FIELD_REF;
    $table = Doctrine_Core::getTable('PetitionSigning');

    printf("Campaign: %s\nPetition: %s\n",
      $campaign['name'],
      $petition['name']
    );
    $first_id = false;

    $first_line = null;
    if (($handle = @fopen($filename, "r")) !== false)
    {
      $con = $table->getConnection();
      $con->beginTransaction();
      echo "Begin transaction\n";
      try
      {
        $emails = array();

        $i = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== false)
        {
          $i++;
          if ($i % 100 == 0) echo "$i\n";

          //if ($i > 300) break;
          if (!is_array($first_line))
          {
            $first_line = $data;
          }
          else
          {
            $line = array_combine($first_line, $data);
            $signing = new PetitionSigning();
            $signing->setPetitionId($petition_id);
            $signing->setPetitionStatus($petition['status']);
            $signing->setPetitionEnabled($petition['status'] != 7 ? 1 : 0);
            $signing->setStatus(PetitionSigning::STATUS_COUNTED);
            $signing->setCampaignId($campaign['id']);
            foreach ($formfields as $formfield)
            {
              switch ($formfield)
              {
//                case 'created_at':
//                  $signing->setCreatedAt($line[$formfield]);
//                  break;
//                case 'updated_at':
//                  $signing->setUpdatedAt($line[$formfield]);
//                  break;
                case Petition::FIELD_FULLNAME:
                  $fullname = array();
                  if (isset($line[Petition::FIELD_FIRSTNAME])) $fullname[] = trim($line[Petition::FIELD_FIRSTNAME]);
                  if (isset($line[Petition::FIELD_LASTNAME]))  $fullname[] = trim($line[Petition::FIELD_LASTNAME]);
                  if (isset($line[Petition::FIELD_FULLNAME]))  $fullname[] = trim($line[Petition::FIELD_FULLNAME]);
                  $fullname = join(' ', $fullname);
                  $signing->setField(Petition::FIELD_FULLNAME, $fullname);
                  break;
                case Petition::FIELD_SUBSCRIBE:
                  if (isset($line[Petition::FIELD_SUBSCRIBE]) && $line[Petition::FIELD_SUBSCRIBE] == '1')
                    $signing->setField(Petition::FIELD_SUBSCRIBE, array('yes'));
                  else
                    $signing->setField(Petition::FIELD_SUBSCRIBE, array());
                  break;
                default:
                  if (isset($line[$formfield])) $signing->setField($formfield, $line[$formfield]);
              }
            }
            $signing->save();
            $search_table->savePetitionSigning($signing, false);
            if ($first_id === false) $first_id = $signing->getId();
            $email = $signing->getField(Petition::FIELD_EMAIL);
            if (is_string($email)) $emails[] = trim($email);
            $signing->free();
          }
        }

        echo "checking duplicates\n";
        while (count($emails) > 0)
        {
          $i = 0;
          $where_param = array();
          $where = array();
          while ($i++ < 100)
          {
            $email = array_shift($emails);
            if (empty($email)) $break;
            $where_param[] = $email;
            $where[] = '(ps.email = ?)';
          }
          echo count($emails) . "\n";

          $duplicates = $table->createQuery('ps')
            ->where('ps.petition_id = ?', $petition_id)
            ->andWhere('ps.id < ?', $first_id)
            ->andWhere(join(' OR ', $where), $where_param)
            ->execute();
          foreach ($duplicates as $duplicate) $duplicate->setStatus(PetitionSigning::STATUS_DUPLICATE);
          $duplicates->save();
          $duplicates->free();
        }

        echo "Commit transaction";
        $con->commit();
        echo ".\n";
      }
      catch (Exception $e)
      {
        $con->rollback();
        echo "DB error. (rollback)\n";
      }
      fclose($handle);
    }
    else
    {
      echo "File error.\n";
      return;
    }
  }
}
