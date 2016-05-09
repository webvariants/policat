<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class d_widgetComponents extends policatComponents {

  public function executeList() {
    $page = isset($this->page) ? $this->page : 1;

    if (isset($this->petition)) {
      $this->form = new FilterWidgetForm();
      $this->form->bindSelf('p' . $this->petition->getId());

      $this->widgets = new policatPager(WidgetTable::getInstance()->queryByPetition($this->petition), $page, 'widget_pager_petition', array('id' => $this->petition->getId()), true, 20, $this->form);
    } else {
      $this->form = new FilterWidgetForm(array(), array(
          FilterWidgetForm::WITH_CAMPAIGN => true,
          FilterWidgetForm::USER => $this->getGuardUser()
      ));
      $this->form->bindSelf('all');

      $this->widgets = new policatPager(WidgetTable::getInstance()->queryByUser($this->getGuardUser()), $page, 'widget_pager', array(), true, 20, $this->form);
    }

    $this->csrf_token = UtilCSRF::gen('widget_data_owner');

    $this->csrf_token_revoke = UtilCSRF::gen('widget_revoke_data_owner');
  }

  public function executeCopy() {
    if ($this->getGuardUser()->isCampaignAdmin($this->petition->getCampaign())) {
      $this->form = new WidgetsCopyFollowForm(array(), array(
          'petition' => $this->petition,
          'user' => $this->getGuardUser()
      ));
      
      $this->followedBy = $this->petition->getFollowedByPetitions();
      if (!$this->followedBy->count()) {
        $this->followedBy = null;
      }
    }
  }

}
