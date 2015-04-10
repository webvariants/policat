<?php

/**
 * User table.
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage model
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: PluginsfGuardUserTable.class.php 25546 2009-12-17 23:27:55Z Jonathan.Wage $
 */
abstract class PluginsfGuardUserTable extends Doctrine_Table
{
  const FILTER_SEARCH = 'search';

  /**
   * Retrieves a sfGuardUser object by username and is_active flag.
   *
   * @param  string  $username The username
   * @param  boolean $isActive The user's status
   *
   * @return sfGuardUser
   */
  public function retrieveByUsername($username, $isActive = true)
  {
    $query = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')
      ->where('u.username = ?', $username)
      ->addWhere('u.is_active = ?', $isActive)
    ;

    return $query->fetchOne();
  }

  /**
   * Retrieves a sfGuardUser object by username or email_address and is_active flag.
   *
   * @param  string  $username The username
   * @param  boolean $isActive The user's status
   *
   * @return sfGuardUser
   */
  public function retrieveByUsernameOrEmailAddress($username, $isActive = true)
  {
    $query = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')
      ->where('u.username = ? OR u.email_address = ?', array($username, $username))
      ->addWhere('u.is_active = ?', $isActive)
    ;

    return $query->fetchOne();
  }

  /**
   *
   * @param Doctrine_Query $query
   * @param FilterContactForm $filter
   * @return Doctrine_Query
   */
  public function filter(Doctrine_Query $query, $filter) {
    if (!$filter)
      return $query;

    $alias = $query->getRootAlias();

    $search = trim($filter->getValue(self::FILTER_SEARCH));
    if ($search) {
      $query->andWhere('concat(' . $alias . '.email_address, " ", '. $alias .'.first_name, " ", '. $alias .'.last_name) LIKE ?', '%' . $search . '%');
    }

    return $query;
  }
}
