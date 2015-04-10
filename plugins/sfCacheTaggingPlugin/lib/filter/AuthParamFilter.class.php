<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Adds additional $_GET parameter to allow caching authenticated
   * private data.
   * Symfony cache block key will be based on "user_id" argument too.
   *
   * @package sfCacheTaggingPlugin
   * @subpackage filters
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class AuthParamFilter extends sfFilter
  {
    public function execute ($filterChain)
    {
      $context = $this->getContext();

      if ($this->isFirstCall())
      {
        if ($context->getUser()->isAuthenticated())
        {
          $context
            ->getRequest()
            ->addGetParameters(
              array(
                'user_id' => $context->getUser()->getId(),
              )
            )
          ;
        }
      }

      $filterChain->execute();
    }
  }
