<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * defaultActions module.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions
{
  /**
   * Congratulations page for creating an application
   *
   */
  public function executeIndex()
  {
  }

  /**
   * Congratulations page for creating a module
   *
   */
  public function executeModule()
  {
  }

  /**
   * Error page for page not found (404) error
   *
   */
  public function executeError404()
  {
  }

  /**
   * Warning page for restricted area - requires login
   *
   */
  public function executeSecure()
  {
  }

  /**
   * Warning page for restricted area - requires credentials
   *
   */
  public function executeLogin()
  {
  }

  /**
   * Module disabled in settings.yml
   *
   */
  public function executeDisabled()
  {
  }
}
