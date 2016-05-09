<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class OrderNewForm extends OrderForm {

  const OPTION_CAMPAIGN = 'campaign';
  const OPTION_PRENVENT_SAVE = 'prevent_save';
  const OPTION_MANUAL_QUOTA = 'manual';

  private $choices = null;

  public function configure() {
    parent::configure();

    unset($this['paid_at'], $this['status'], $this['tax_note']);

    if (!$this->getOption(self::OPTION_MANUAL_QUOTA)) {
      $this->setWidget('product', new sfWidgetFormChoice(array(
          'choices' => $this->getProductChoices(),
          'renderer_class' => 'sfWidgetFormSelectProduct',
          'expanded' => true,
          'label' => false,
          'translate_choices' => false
      )));

      $this->setValidator('product', new sfValidatorChoice(array(
          'choices' => array_keys($this->getProductChoices())
      )));
    }

    foreach (array('first_name', 'last_name', 'organisation', 'street', 'city', 'post_code') as $field) {
      $this->getValidator($field)->setOption('required', true);
    }

    $this->setWidget('country', new sfWidgetFormI18nChoiceCountry());
    $this->setValidator('country', new sfValidatorI18nChoiceCountry());

    $this->getWidgetSchema()->setLabel('vat', 'VAT no.');
    $this->getWidgetSchema()->setHelp('vat', 'Leave this field empty if you\'re an individual, group or NGO without a VAT identification number (VATIN). If you add your VATIN and are based in an EU member state except Germany, you will not be charged VAT. Make sure to include the full number preceded by the country code. Check the validity of your VATIN here: http://ec.europa.eu/taxation_customs/vies/');

    $this->mergePostValidator(new ValidatorVat(null, array('country' => 'country', 'vat' => 'vat')));
  }

  private function getProducts() {
    return ProductTable::getInstance()->queryAll()->execute();
  }

  private function getProductChoices() {
    if ($this->choices === null) {
      $products = $this->getProducts();
      $choices = array();
      foreach ($products as $product) {
        /* @var $product Product */
        $choices[$product->getId()] = $product;
      }

      $campaign = $this->getOption(self::OPTION_CAMPAIGN);
      /* @var $campaign Campaign */

      if ($campaign->getQuotaId()) {
        $quota = $campaign->getQuota();
        if ($quota->getUpgradedBy()->isNew() && $quota->getPrice() && $quota->getEmails()) {
          foreach ($products as $product) {
            $diff_emails = $product->getEmails() - $quota->getEmails();
            $diff_price = $product->getPrice() - $quota->getPrice();

            if ($diff_emails > 0 && $diff_price > 0) {
              $update_product = new Product();
              $update_product->setName('Upgrade to ' . $product->getName());
              $update_product->setEmails($diff_emails);
              $update_product->setDays($product->getDays());
              $update_product->setPrice($diff_price);

              /* @var $product Product */
              $choices['u' . $product->getId()] = $update_product;
            }
          }
        }
      }

      $this->choices = $choices;
    }

    return $this->choices;
  }

  protected function doUpdateObject($values) {
    parent::doUpdateObject($values);

    $this->getObject()->setTax(CountryTaxTable::taxForCountryVat($this->getValue('country'), trim($this->getValue('vat'))));
    $this->getObject()->setTaxNote(CountryTaxTable::noteForCountryVat($this->getValue('country'), trim($this->getValue('vat'))));
  }

  protected function doSave($con = null) {
    parent::doSave($con);
    $campaign = $this->getOption(self::OPTION_CAMPAIGN);
    /* @var $campaign Campaign */

    $order = $this->getObject();
    if (!$this->getOption(self::OPTION_MANUAL_QUOTA)) {
      $products = $this->getProductChoices();
      $product = $products[$this->getValue('product')];
      /* @var $product Product */
      $quota = new Quota();
      $quota->setUser($order->getUser());
      $quota->copyProduct($product);
      if ($product->isNew()) {
        $quota->setUpgradeOf($campaign->getQuota());
      }
      $quota->setCampaign($campaign);
      $order->Quotas->add($quota);
    } else {
      $quota = $this->getOption(self::OPTION_MANUAL_QUOTA);
      $quota->setCampaign($campaign);
      $quota->setUser($order->getUser());
      $order->Quotas->add($quota);
    }
    if ($this->getOption(self::OPTION_PRENVENT_SAVE)) {
      return;
    }

    $quota->save();
    $campaign->setOrder($order);
    $campaign->save();
  }

  public function saveObject($con = null) {
    if ($this->getOption(self::OPTION_PRENVENT_SAVE)) {
      return;
    }

    parent::saveObject($con);
  }

}
