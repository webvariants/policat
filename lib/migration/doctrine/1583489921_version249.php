<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version249 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('widget', 'from_name', 'string', '80', array(
             ));
        $this->addColumn('widget', 'from_email', 'string', '80', array(
             ));
    }

    public function down()
    {
        $this->removeColumn('widget', 'from_name');
        $this->removeColumn('widget', 'from_email');
    }
}