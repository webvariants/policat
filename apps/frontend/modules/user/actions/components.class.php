<?php

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