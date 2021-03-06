<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version48 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('petition_rights', 'petition_rights_petition_id_petition_id', array(
             'name' => 'petition_rights_petition_id_petition_id',
             'local' => 'petition_id',
             'foreign' => 'id',
             'foreignTable' => 'petition',
             'onUpdate' => '',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('petition_rights', 'petition_rights_user_id_sf_guard_user_id', array(
             'name' => 'petition_rights_user_id_sf_guard_user_id',
             'local' => 'user_id',
             'foreign' => 'id',
             'foreignTable' => 'sf_guard_user',
             'onUpdate' => '',
             'onDelete' => 'CASCADE',
             ));
        $this->addIndex('petition_rights', 'petition_rights_petition_id', array(
             'fields' => 
             array(
              0 => 'petition_id',
             ),
             ));
        $this->addIndex('petition_rights', 'petition_rights_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('petition_rights', 'petition_rights_petition_id_petition_id');
        $this->dropForeignKey('petition_rights', 'petition_rights_user_id_sf_guard_user_id');
        $this->removeIndex('petition_rights', 'petition_rights_petition_id', array(
             'fields' => 
             array(
              0 => 'petition_id',
             ),
             ));
        $this->removeIndex('petition_rights', 'petition_rights_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));
    }
}