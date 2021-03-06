<?php

/**
 * BaseMember
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int                                $id                                      Type: integer(4), primary key
 * @property int                                $sf_guard_user_id                        Type: integer(4)
 * @property int                                $campaign_id                             Type: integer(4)
 * @property Doctrine_Collection|Group[]        $Group                                   
 * @property sfGuardUser                        $sfGuardUser                             
 * @property Campaign                           $Campaign                                
 * @property Doctrine_Collection|GroupMember[]  $GroupMember                             
 *  
 * @method int                                  getId()                                  Type: integer(4), primary key
 * @method int                                  getSfGuardUserId()                       Type: integer(4)
 * @method int                                  getCampaignId()                          Type: integer(4)
 * @method Doctrine_Collection|Group[]          getGroup()                               
 * @method sfGuardUser                          getSfGuardUser()                         
 * @method Campaign                             getCampaign()                            
 * @method Doctrine_Collection|GroupMember[]    getGroupMember()                         
 *  
 * @method Member                               setId(int $val)                          Type: integer(4), primary key
 * @method Member                               setSfGuardUserId(int $val)               Type: integer(4)
 * @method Member                               setCampaignId(int $val)                  Type: integer(4)
 * @method Member                               setGroup(Doctrine_Collection $val)       
 * @method Member                               setSfGuardUser(sfGuardUser $val)         
 * @method Member                               setCampaign(Campaign $val)               
 * @method Member                               setGroupMember(Doctrine_Collection $val) 
 *  
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseMember extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('member');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('sf_guard_user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('campaign_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));

        $this->option('symfony', array(
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Group', array(
             'refClass' => 'GroupMember',
             'local' => 'member_id',
             'foreign' => 'group_id'));

        $this->hasOne('sfGuardUser', array(
             'local' => 'sf_guard_user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Campaign', array(
             'local' => 'campaign_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('GroupMember', array(
             'local' => 'id',
             'foreign' => 'member_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}