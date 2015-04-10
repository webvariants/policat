<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * @package sfCacheTaggingPlugin
   * @subpackage cache
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfSQLitePDOCache extends sfCache
  {
    /**
     * @var PDO
     */
    protected $dbh = null;

    public function initialize ($options = array())
    {
      parent::initialize($options);

      if ( ! $this->getOption('dsn'))
      {
        throw new sfConfigurationException('Please provide connection DSN');
      }

      $dsn = $this->getOption('dsn');

      $new = false;

      if (false !== strpos($dsn, ':memory:'))
      {
        $new = true;
      }
      else
      {
        $filepath = substr($dsn, strpos($dsn, ':') + 1);

        if ( ! is_dir($dirname = dirname($filepath)))
        {
          mkdir($dirname, 0755, true);
        }

        $new = ! is_file($filepath);
      }

      $this->dbh = new PDO(
        $dsn,
        $this->getOption('username', null),
        $this->getOption('password', null),
        $this->getOption('driver_options', array())
      );

      $this->getBackend()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->getBackend()->sqliteCreateFunction('regexp', 'preg_match');

      if ($new)
      {
        $this->createSchema();
      }
    }

    /**
     * @return PDO
     */
    public function getBackend ()
    {
      return $this->dbh;
    }

    public function clean ($mode = self::ALL)
    {
      if (sfCache::OLD === $mode)
      {
        $query = "DELETE FROM cache WHERE timeout < ?";
        $stmt = $this->getBackend()->prepare($query);
        $stmt->bindValue(1, time(), PDO::PARAM_INT);
        $stmt->execute();
      }
      else
      {
        $this->getBackend()->exec("DELETE FROM cache");
      }
    }

    public function get ($key, $default = null)
    {
      $data = $this->getColumn($key, 'data');

      return false === $data ? $default : $data;
    }

    public function getLastModified ($key)
    {
      return (int) $this->getColumn($key, 'last_modified');
    }

    public function getTimeout ($key)
    {
      return (int) $this->getColumn($key, 'timeout');
    }

    public function has ($key)
    {
      return false !== $this->getColumn($key, 'key');
    }

    public function remove ($key)
    {
      $query = 'DELETE FROM cache WHERE key = ?';

      $stmt = $this->getBackend()->prepare($query);

      $stmt->bindValue(1, $key, PDO::PARAM_STR);

      $stmt->execute();

      return 1 === $stmt->rowCount();
    }

    public function removePattern ($pattern)
    {
      $query = "DELETE FROM cache WHERE key REGEXP ?";

      $stmt = $this->getBackend()->prepare($query);

      $stmt->bindValue(1, $this->patternToRegexp($pattern), PDO::PARAM_STR);

      return $stmt->execute();
    }

    public function set ($key, $data, $lifetime = null)
    {
      $acf = $this->getOption('automatic_cleaning_factor');

      if (($acf > 0) && (1 == mt_rand(1, $acf)))
      {
        $this->clean(sfCache::OLD);
      }

      $query = "INSERT OR REPLACE INTO cache (key, data, timeout, last_modified) "
        . "VALUES (?, ?, ?, ?)";

      $stmt = $this->getBackend()->prepare($query);

      $now = time();
      $stmt->bindValue(1, $key, PDO::PARAM_STR);
      $stmt->bindValue(2, $data, PDO::PARAM_STR);
      $stmt->bindValue(3, $now + $this->getLifetime($lifetime), PDO::PARAM_INT);
      $stmt->bindValue(4, $now, PDO::PARAM_INT);

      return $stmt->execute();
    }

    protected function getColumn ($key, $columnName)
    {
      $query = "SELECT {$columnName} FROM cache WHERE key = ? AND timeout > ?";

      $stmt = $this->getBackend()->prepare($query);

      $stmt->bindValue(1, $key, PDO::PARAM_STR);
      $stmt->bindValue(2, time(), PDO::PARAM_INT);

      $stmt->execute();

      return $stmt->fetchColumn(0);
    }

    protected function createSchema ()
    {
      $statement = <<< SCHEMA

        BEGIN TRANSACTION;

        CREATE TABLE cache (
          key           VARCHAR(255),
          data          LONGVARCHAR,
          timeout       TIMESTAMP,
          last_modified TIMESTAMP
        );

        CREATE UNIQUE INDEX [cache_unique] ON [cache] ([key]);

        COMMIT;

SCHEMA;

      $this->getBackend()->exec($statement);
    }
  }