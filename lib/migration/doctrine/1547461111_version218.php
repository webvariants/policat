<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version218 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('petition_text', 'digest_subject', 'string', '', array(
             'notnull' => '1',
             'default' => '',
             ));
        $this->addColumn('petition_text', 'digest_body_intro', 'clob', '', array(
             ));
        $this->addColumn('petition_text', 'digest_body_outro', 'clob', '', array(
             ));
    }

    public function down()
    {
        $this->removeColumn('petition_text', 'digest_subject');
        $this->removeColumn('petition_text', 'digest_body_intro');
        $this->removeColumn('petition_text', 'digest_body_outro');
    }
}