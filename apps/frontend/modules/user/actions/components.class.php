<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class userComponents extends policatComponents {

  public function executeList() {
    $filter_form = new FilterUserForm();
    $filter_form->bindSelf('user');
    $this->form = $filter_form;

    $page = isset($this->page) ? $this->page : 1;

    $query = $filter_form->filter(sfGuardUserTable::getInstance()->queryAll($this->getUser()->isSuperAdmin()));
    
    $this->users = new policatPager($query, $page, 'user_pager', array(), true, 20);
    
  }

}