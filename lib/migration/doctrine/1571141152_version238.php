<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version238 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('petition_signing', 'ref_hash', 'string', '160', array(
             'notnull' => '',
             ));
    }

    public function down()
    {

    }
}