<?php

class MailMessageTable extends Doctrine_Table
{
  public function getSpooledMessages()
  {
    return $this->createQuery('m');
  }

}