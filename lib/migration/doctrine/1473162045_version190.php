<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version190 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addIndex('petition_signing', 'signing_list_name1', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'fullname',
              3 => 'id',
             ),
             ));
        $this->addIndex('petition_signing', 'signing_list_name2', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'lastname',
              3 => 'id',
             ),
             ));
        $this->addIndex('petition_signing', 'signing_list_city', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'city',
              3 => 'id',
             ),
             ));
        $this->addIndex('petition_signing', 'signing_list_country', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'country',
              3 => 'city',
              4 => 'id',
             ),
             ));
    }

    public function down()
    {
        $this->removeIndex('petition_signing', 'signing_list_name1', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'fullname',
              3 => 'id',
             ),
             ));
        $this->removeIndex('petition_signing', 'signing_list_name2', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'lastname',
              3 => 'id',
             ),
             ));
        $this->removeIndex('petition_signing', 'signing_list_city', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'city',
              3 => 'id',
             ),
             ));
        $this->removeIndex('petition_signing', 'signing_list_country', array(
             'fields' => 
             array(
              0 => 'petition_id',
              1 => 'status',
              2 => 'country',
              3 => 'city',
              4 => 'id',
             ),
             ));
    }
}