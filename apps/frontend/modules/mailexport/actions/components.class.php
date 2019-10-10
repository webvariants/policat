<?php
/*
 * Copyright (c) 2019, webvariants GmbH, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class mailexportComponents extends policatComponents {

  public function executeSetting() {
    if (!$this->getGuardUser()->isDataOwnerOfCampaign($this->petition->getCampaign())) {
      $this->show = false;
      return;
    }
    $this->show = true;
    $this->form = new MailExportSettingForm($this->petition->getMailexportData(), ['petition' => $this->petition]);
    $this->test_csrf_token = UtilCSRF::gen('mailexport_test', $this->petition->getId());
    $this->enabled_services = [];
    foreach (MailExport::getServices() as $name => $service) {
      if ($service->checkEnabled($this->petition)) {
        $this->enabled_services[$name] = $service->getName();
      }
    }
  }

}
