<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * When user disables sf_cache setting, application environment should
   * work with sf_cache=Off, this exception helps to solve this problem
   *
   * @package sfCacheTaggingPlugin
   * @subpackage exception
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfCacheDisabledException extends sfCacheException
  {

  }
