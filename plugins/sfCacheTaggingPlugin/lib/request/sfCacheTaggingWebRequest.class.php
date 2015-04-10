<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Custom HTTP Request class to access the encapsulated
   * private variable "$this->getParameters"
   *
   * @package sfCacheTaggingPlugin
   * @subpackage request
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfCacheTaggingWebRequest extends sfWebRequest
  {
    /**
     * Appends new _GET parameters to the existing.
     * Used in AuthParamFilter to add custom parameters on the fly.
     *
     * @var array $params
     * @return sfCacheTaggingWebRequest
     */
    public function addGetParameters ($params)
    {
      $this->getParameters = array_merge($this->getParameters, $params);

      return $this;
    }

    /**
     * Deletes all registered _GET parameters.
     *
     * Used to cache something without depending on whether user is
     * authenticated or not. All users should see the same content.
     * Real life situation: Page with "Terms & Conditions"
     */
    public function deleteGetParameters ()
    {
      $this->getParameters = array();
    }

    /**
     * Return only _GET parameters + cleaning cache attribute for
     * authorized users @see AuthParamFilter
     *
     * Used when page contains listing with filter (sfFormFilter) and
     * Pagination/Sorting should be avoided from custom "user_id" parameters.
     *
     * @return array
     */
    public function getFilteredGetParameters ()
    {
      return array_diff_key($this->getParameters, array('user_id' => null));
    }
  }
