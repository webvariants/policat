<?php

/**
 * BasePetitionSigningSearch
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int                    $id                                      Type: integer(8), primary key
 * @property string                 $keyword                                 Type: string(48), primary key
 * @property PetitionSigning        $PetitionSigning                         
 *  
 * @method int                      getId()                                  Type: integer(8), primary key
 * @method string                   getKeyword()                             Type: string(48), primary key
 * @method PetitionSigning          getPetitionSigning()                     
 *  
 * @method PetitionSigningSearch    setId(int $val)                          Type: integer(8), primary key
 * @method PetitionSigningSearch    setKeyword(string $val)                  Type: string(48), primary key
 * @method PetitionSigningSearch    setPetitionSigning(PetitionSigning $val) 
 *  
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePetitionSigningSearch extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('petition_signing_search');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'primary' => true,
             'length' => 8,
             ));
        $this->hasColumn('keyword', 'string', 48, array(
             'type' => 'string',
             'primary' => true,
             'length' => 48,
             ));


        $this->index('search_keyword_idx', array(
             'fields' => 
             array(
              0 => 'keyword',
             ),
             ));
        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('PetitionSigning', array(
             'local' => 'id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}