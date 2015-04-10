<?php

  /**
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Tag holder to set only newest tags if same tag already exists.
   *
   * @package sfCacheTaggingPlugin
   * @subpackage util
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfTagNamespacedParameterHolder extends sfNamespacedParameterHolder
  {
    /**
     * Removes tag from holder
     *
     * @see parent::remove()
     *
     * @param string  $tagName
     * @param mixed   $default
     * @param mixed   $ns
     *
     * @return null
     */
    public function remove ($tagName, $default = null, $ns = null)
    {
      if (gettype($tagName) !== 'string')
      {
        throw new InvalidArgumentException(sprintf(
          'Name should be typeof "string" (given "%s")', gettype($tagName)
        ));
      }

      return parent::remove($tagName, $default, $ns);
    }

    /**
     * Adds tag with its version to the holder
     *
     * @param string  $tagName
     * @param numeric $tagVersion
     * @param mixed   $ns
     *
     * @return null
     */
    public function set ($tagName, $tagVersion, $ns = null)
    {
      if (! $ns)
      {
        $ns = $this->default_namespace;
      }

      if (! is_string($tagName))
      {
        throw new InvalidArgumentException(sprintf(
          'Called "%s" with invalid first argument "$tagName" type "%s". ' .
            'Acceptable type is: "string"',
          __METHOD__,
          gettype($tagName)
        ));
      }

      if (! is_numeric($tagVersion))
      {
        throw new InvalidArgumentException(sprintf(
          'Called "%s" with invalid second argument "$tagVersion" type "%s". ' .
            'Acceptable type is: "numeric"',
          __METHOD__,
          gettype($tagVersion)
        ));
      }

      if (! isset($this->parameters[$ns]))
      {
        $this->parameters[$ns] = array();
      }

      # skip old tag versions
      if (
          ! isset($this->parameters[$ns][$tagName]) ||
          $tagVersion > $this->parameters[$ns][$tagName]
      )
      {
        $this->parameters[$ns][$tagName] = $tagVersion;
      }
    }

    /**
     * Adds parameters to the holder
     *
     * @param mixed $parameters
     * @param mixed $ns
     *
     * @return null
     */
    public function add ($parameters, $ns = null)
    {
      if (! is_array($parameters))
      {
        throw new InvalidArgumentException('Parameters is not type of Array');
      }

      foreach ($parameters as $name => $value)
      {
        $this->set($name, $value, $ns);
      }
    }
  }