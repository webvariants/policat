<?php

/**
 * BaseProduct
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int                          $id                                 Type: integer(4), primary key
 * @property string                       $name                               Type: string(120), unique
 * @property float                        $price                              Type: decimal(10)
 * @property int                          $days                               Type: integer(4)
 * @property int                          $emails                             Type: integer(4)
 * @property int                          $subscription                       Type: integer(1)
 * @property Doctrine_Collection|Quota[]  $Quotas                             
 *  
 * @method int                            getId()                             Type: integer(4), primary key
 * @method string                         getName()                           Type: string(120), unique
 * @method float                          getPrice()                          Type: decimal(10)
 * @method int                            getDays()                           Type: integer(4)
 * @method int                            getEmails()                         Type: integer(4)
 * @method int                            getSubscription()                   Type: integer(1)
 * @method Doctrine_Collection|Quota[]    getQuotas()                         
 *  
 * @method Product                        setId(int $val)                     Type: integer(4), primary key
 * @method Product                        setName(string $val)                Type: string(120), unique
 * @method Product                        setPrice(float $val)                Type: decimal(10)
 * @method Product                        setDays(int $val)                   Type: integer(4)
 * @method Product                        setEmails(int $val)                 Type: integer(4)
 * @method Product                        setSubscription(int $val)           Type: integer(1)
 * @method Product                        setQuotas(Doctrine_Collection $val) 
 *  
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseProduct extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('product');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('name', 'string', 120, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => true,
             'length' => 120,
             ));
        $this->hasColumn('price', 'decimal', 10, array(
             'type' => 'decimal',
             'notnull' => true,
             'scale' => 2,
             'length' => 10,
             ));
        $this->hasColumn('days', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 4,
             ));
        $this->hasColumn('emails', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 4,
             ));
        $this->hasColumn('subscription', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 1,
             ));

        $this->option('symfony', array(
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Quota as Quotas', array(
             'local' => 'id',
             'foreign' => 'product_id'));

        $cachetaggable0 = new Doctrine_Template_Cachetaggable();
        $this->actAs($cachetaggable0);
    }
}