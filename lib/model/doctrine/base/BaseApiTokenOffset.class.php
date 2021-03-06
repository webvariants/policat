<?php

/**
 * BaseApiTokenOffset
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int               $id                                Type: integer(4), primary key
 * @property int               $petition_api_token_id             Type: integer(4)
 * @property string            $country                           Type: string(5)
 * @property int               $addnum                            Type: integer(4)
 * @property PetitionApiToken  $ApiToken                          
 *  
 * @method int                 getId()                            Type: integer(4), primary key
 * @method int                 getPetitionApiTokenId()            Type: integer(4)
 * @method string              getCountry()                       Type: string(5)
 * @method int                 getAddnum()                        Type: integer(4)
 * @method PetitionApiToken    getApiToken()                      
 *  
 * @method ApiTokenOffset      setId(int $val)                    Type: integer(4), primary key
 * @method ApiTokenOffset      setPetitionApiTokenId(int $val)    Type: integer(4)
 * @method ApiTokenOffset      setCountry(string $val)            Type: string(5)
 * @method ApiTokenOffset      setAddnum(int $val)                Type: integer(4)
 * @method ApiTokenOffset      setApiToken(PetitionApiToken $val) 
 *  
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseApiTokenOffset extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('api_token_offset');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('petition_api_token_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('country', 'string', 5, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('addnum', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 4,
             ));


        $this->index('p_counter_idx', array(
             'fields' => 
             array(
              0 => 'petition_api_token_id',
              1 => 'country',
             ),
             ));
        $this->option('form', false);
        $this->option('filter', false);
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('PetitionApiToken as ApiToken', array(
             'local' => 'petition_api_token_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}