<?php

class MailMessageTable extends Doctrine_Table
{
  public function getSpooledMessages()
  {
    return $this->createQuery('m')->orderBy('id')->andWhere('id > 0'); // id > 0 makes index range scan
  }

}