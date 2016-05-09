<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorVat extends sfValidatorSchema {

  protected function configure($options = array(), $messages = array()) {
    $this->addRequiredOption('country');
    $this->addRequiredOption('vat');

    parent::configure($options, $messages);
  }

  protected function doClean($values) {
    $county = $values[$this->getOption('country')];
    $vat = $values[$this->getOption('vat')];

    $errorSchema = new sfValidatorErrorSchema($this);

    if ($vat && in_array($county, array('BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'ES', 'FI', 'FR', 'GB', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'SE', 'SI', 'SK', 'RO'))) {
      $vatclean = str_replace(array(' ', '.', '-', ',', ', '), '', trim($vat));
      if (mb_strlen($vatclean) > 3) {
        $cc = substr($vatclean, 0, 2);
        if (ctype_digit($cc)) {
          $cc = $county;
          $vn = $vatclean;
        } else {
          if (strtoupper($cc) !== $county) {
            $errorSchema->addError(new sfValidatorError($this, 'Invalid VAT (not matching country)'), $this->getOption('vat'));
          }
          $vn = substr($vatclean, 2);
        }
        $client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");

        if ($client) {
          $params = array('countryCode' => $cc, 'vatNumber' => $vn);
          try {
            $response = $client->checkVat($params);
            if ($response->valid == true) {
              // VAT-ID is valid
            } else {
              // VAT-ID is NOT valid
              $errorSchema->addError(new sfValidatorError($this, 'Invalid VAT'), $this->getOption('vat'));
            }
          } catch (SoapFault $e) {
            // ignore
          }
        }
      } else {
        $errorSchema->addError(new sfValidatorError($this, 'Invalid VAT (too short)'), $this->getOption('vat'));
      }
    }

    if ($errorSchema->count()) {
      throw $errorSchema;
    }

    return $values;
  }

}
