<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version230 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('petition', 'with_extra2', 'integer', '1', array(
             'notnull' => '1',
             'default' => '0',
             ));
        $this->addColumn('petition', 'with_extra3', 'integer', '1', array(
             'notnull' => '1',
             'default' => '0',
             ));
        $this->addColumn('petition_signing', 'extra2', 'string', '100', array(
             'notnull' => '',
             ));
        $this->addColumn('petition_signing', 'extra3', 'string', '100', array(
             'notnull' => '',
             ));
        $this->addColumn('petition_text', 'label_extra2', 'string', '80', array(
             'notnull' => '',
             ));
        $this->addColumn('petition_text', 'placeholder_extra2', 'string', '80', array(
             'notnull' => '',
             ));
        $this->addColumn('petition_text', 'label_extra3', 'string', '80', array(
             'notnull' => '',
             ));
        $this->addColumn('petition_text', 'placeholder_extra3', 'string', '80', array(
             'notnull' => '',
             ));
    }

    public function down()
    {
        $this->removeColumn('petition', 'with_extra2');
        $this->removeColumn('petition', 'with_extra3');
        $this->removeColumn('petition_signing', 'extra2');
        $this->removeColumn('petition_signing', 'extra3');
        $this->removeColumn('petition_text', 'label_extra2');
        $this->removeColumn('petition_text', 'placeholder_extra2');
        $this->removeColumn('petition_text', 'label_extra3');
        $this->removeColumn('petition_text', 'placeholder_extra3');
    }
}