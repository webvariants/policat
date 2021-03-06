<?php

/**
 * BaseQueryCache
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string      $id                       Type: string(255), primary key
 * @property object      $cacheData                Type: blob
 * @property string      $expire                   Type: timestamp, Timestamp in ISO-8601 format (YYYY-MM-DD HH:MI:SS)
 *  
 * @method string        getId()                   Type: string(255), primary key
 * @method object        getCachedata()            Type: blob
 * @method string        getExpire()               Type: timestamp, Timestamp in ISO-8601 format (YYYY-MM-DD HH:MI:SS)
 *  
 * @method QueryCache    setId(string $val)        Type: string(255), primary key
 * @method QueryCache    setCachedata(object $val) Type: blob
 * @method QueryCache    setExpire(string $val)    Type: timestamp, Timestamp in ISO-8601 format (YYYY-MM-DD HH:MI:SS)
 *  
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseQueryCache extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('query_cache');
        $this->hasColumn('id', 'string', 255, array(
             'type' => 'string',
             'primary' => true,
             'length' => 255,
             ));
        $this->hasColumn('data as cacheData', 'blob', null, array(
             'type' => 'blob',
             ));
        $this->hasColumn('expire', 'timestamp', null, array(
             'type' => 'timestamp',
             ));

        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}