<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version231 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('petition', 'openeci_url', 'string', '120', array(
             'notnull' => '',
             ));
        $this->addColumn('petition', 'openeci_channel', 'string', '40', array(
             'notnull' => '',
             ));
    }

    public function down()
    {
        $this->removeColumn('petition', 'openeci_url');
        $this->removeColumn('petition', 'openeci_channel');
    }
}