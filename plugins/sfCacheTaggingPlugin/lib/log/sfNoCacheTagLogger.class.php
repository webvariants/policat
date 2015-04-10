<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Cache key and tag logger
   *
   * @package sfCacheTaggingPlugin
   * @subpackage log
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfNoCacheTagLogger extends sfCacheTagLogger
  {
    public function __construct (array $options = array())
    {

    }

    public function initialize (array $options = array())
    {

    }

    protected function doLog ($char, $key)
    {
      return true;
    }

    public function getOption ($name, $default = null)
    {
      return $default;
    }

    public function getOptions ()
    {
      return array();
    }

    public function log ($char, $key)
    {
      return $this->doLog($char, $key);
    }

    public function setOption ($name, $value)
    {

    }
  }