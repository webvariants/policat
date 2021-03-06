<?php

/**
 * BasesfGuardGroup
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string                                        $name                                               Type: string(255), unique
 * @property string                                        $description                                        Type: string(1000)
 * @property int                                           $id                                                 Type: integer(4), primary key
 * @property Doctrine_Collection|sfGuardUser[]             $Users                                              
 * @property Doctrine_Collection|sfGuardPermission[]       $Permissions                                        
 * @property Doctrine_Collection|sfGuardGroupPermission[]  $sfGuardGroupPermission                             
 * @property Doctrine_Collection|sfGuardUserGroup[]        $sfGuardUserGroup                                   
 *  
 * @method string                                          getName()                                           Type: string(255), unique
 * @method string                                          getDescription()                                    Type: string(1000)
 * @method int                                             getId()                                             Type: integer(4), primary key
 * @method Doctrine_Collection|sfGuardUser[]               getUsers()                                          
 * @method Doctrine_Collection|sfGuardPermission[]         getPermissions()                                    
 * @method Doctrine_Collection|sfGuardGroupPermission[]    getSfGuardGroupPermission()                         
 * @method Doctrine_Collection|sfGuardUserGroup[]          getSfGuardUserGroup()                               
 *  
 * @method sfGuardGroup                                    setName(string $val)                                Type: string(255), unique
 * @method sfGuardGroup                                    setDescription(string $val)                         Type: string(1000)
 * @method sfGuardGroup                                    setId(int $val)                                     Type: integer(4), primary key
 * @method sfGuardGroup                                    setUsers(Doctrine_Collection $val)                  
 * @method sfGuardGroup                                    setPermissions(Doctrine_Collection $val)            
 * @method sfGuardGroup                                    setSfGuardGroupPermission(Doctrine_Collection $val) 
 * @method sfGuardGroup                                    setSfGuardUserGroup(Doctrine_Collection $val)       
 *  
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasesfGuardGroup extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('sf_guard_group');
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'unique' => true,
             'length' => 255,
             ));
        $this->hasColumn('description', 'string', 1000, array(
             'type' => 'string',
             'length' => 1000,
             ));
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));

        $this->option('options', NULL);
        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('sfGuardUser as Users', array(
             'refClass' => 'sfGuardUserGroup',
             'local' => 'group_id',
             'foreign' => 'user_id'));

        $this->hasMany('sfGuardPermission as Permissions', array(
             'refClass' => 'sfGuardGroupPermission',
             'local' => 'group_id',
             'foreign' => 'permission_id'));

        $this->hasMany('sfGuardGroupPermission', array(
             'local' => 'id',
             'foreign' => 'group_id'));

        $this->hasMany('sfGuardUserGroup', array(
             'local' => 'id',
             'foreign' => 'group_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}