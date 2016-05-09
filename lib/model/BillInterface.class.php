<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

interface BillInterface {

  public function getTax();

  public function setCity($city);

  public function setCountry($country);

  public function setFirstName($firstname);

  public function setLastName($lastname);

  public function setOrganisation($organisation);

  public function setPostCode($postcode);

  public function setPrice($price);

  public function setPriceBrutto($price_brutto);

  public function setStreet($street);

  public function setTax($tax);
  
  public function setTaxNote($note);

  public function setVat($vat);

  public function addItemByQuota(Quota $quota);
}
