<?php

class PetitionTextStatusForm extends PetitionTextForm
{
  public function configure()
  {
    parent::configure();

    $this->useFields(array('id', 'status', 'updated_at'));
  }
}