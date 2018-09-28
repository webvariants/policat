<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * CountryTax form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
class CountryTaxForm extends BaseCountryTaxForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap4');
    $this->widgetSchema->setNameFormat('taxcountry[%s]');

    unset($this['object_version']);

    $this->setWidget('country', new sfWidgetFormI18nChoiceCountry());
    $this->setValidator('country', new sfValidatorI18nChoiceCountry(array(
        'required' => true
    )));

    $this->setValidator('tax_no_vat', new sfValidatorNumber(array(
        'min' => 0,
        'max' => 100
    )));

    $this->setValidator('tax_vat', new sfValidatorNumber(array(
        'min' => 0,
        'max' => 100
    )));

    $this->getWidgetSchema()->setLabel('tax_no_vat', 'Tax without VAT-ID in %');
    $this->getWidgetSchema()->setLabel('tax_vat', 'Tax with VAT-ID in %');

    $this->getWidgetSchema()->setLabel('no_vat_note_id', 'Note without VAT-ID');
    $this->getWidgetSchema()->setLabel('vat_note_id', 'Note with VAT-ID');
  }

}
