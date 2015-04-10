<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * execute
   * executes the query and populates the data set
   *
   * Copy&pasted from Doctrine_Query::execute() with small changes
   *
   * @package sfCacheTaggingPlugin
   * @subpackage doctrine
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   * @param array $params
   */
  class Doctrine_Query_Cachetaggable extends Doctrine_Query
  {
    /**
     * @see Doctrine_Query::execute()
     * @param array $params
     * @param int   $hydrationMode
     * @return mixed
     */
    public function execute ($params = array(), $hydrationMode = null)
    {
      // Clean any possible processed params
      $this->_execParams = array();

      $result = null;
      
      if (empty($this->_dqlParts['from']) && empty($this->_sqlParts['from']))
      {
        throw new Doctrine_Query_Exception(
          'You must have at least one component specified in your from.'
        );
      }

      $dqlParams = $this->getFlattenedParams($params);

      $this->_preQuery($dqlParams);

      if ($hydrationMode !== null)
      {
        $this->_hydrator->setHydrationMode($hydrationMode);
      }

      $hydrationMode = $this->_hydrator->getHydrationMode();

      if ($this->_resultCache && $this->_type == self::SELECT)
      {
        $cacheDriver = $this->getResultCacheDriver();
        $hash = $this->getResultCacheHash($params);
        $cached = ($this->_expireResultCache)
          ? false
          : $cacheDriver->fetch($hash);

        if ($cached === false)
        {
          // cache miss
          $stmt = $this->_execute($params);
          $this->_hydrator->setQueryComponents($this->_queryComponents);
          $result = $this->_hydrator->hydrateResultSet(
            $stmt, $this->_tableAliasMap
          );

          $cached = $this->getCachedForm($result);

          if (
              $cacheDriver instanceof Doctrine_Cache_Proxy
            &&
              $result instanceof Doctrine_Collection_Cachetaggable)
          {
            $cacheDriver->saveWithTags(
              $hash,
              $cached,
              $this->getResultCacheLifeSpan(),
              $result->getCacheTags()
            );
          }
          else
          {
            $cacheDriver->save($hash, $cached, $this->getResultCacheLifeSpan());
          }
        }
        else
        {
          $result = $this->_constructQueryFromCache($cached);
        }
      }
      else
      {
        $stmt = $this->_execute($params);

        if (is_integer($stmt))
        {
          $result = $stmt;
        }
        else
        {
          $this->_hydrator->setQueryComponents($this->_queryComponents);
          if (
              $this->_type == self::SELECT
            &&
              $hydrationMode == Doctrine_Core::HYDRATE_ON_DEMAND
          )
          {
            $hydrationDriver = $this->_hydrator->getHydratorDriver(
              $hydrationMode, $this->_tableAliasMap
            );
            $result = new Doctrine_Collection_OnDemand(
              $stmt, $hydrationDriver, $this->_tableAliasMap
            );
          }
          else
          {
            $result = $this->_hydrator->hydrateResultSet(
              $stmt, $this->_tableAliasMap
            );
          }
        }
      }

      if ($this->getConnection()->getAttribute(
        Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS
      ))
      {
        $this->free();
      }

      return $result;
    }
  }

