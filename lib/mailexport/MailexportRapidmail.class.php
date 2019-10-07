<?php

class MailExportRapidmail extends MailExport {
  const BASEURL = 'https://apiv3.emailsys.net/v1/';

  public function formSetup(MailExportSettingForm $form) {
    $form->setWidget('rapidmail_enabled', new WidgetBoolean(['label' => 'Enable Rapidmail']));
    $form->setValidator('rapidmail_enabled', new sfValidatorBoolean());

    $form->setWidget('rapidmail_username', new sfWidgetFormInputText(array('label' => 'Username')));
    $form->setValidator('rapidmail_username', new sfValidatorString(array('max_length' => 100)));

    $form->setWidget('rapidmail_password', new sfWidgetFormInputPassword(array('label' => 'Password', 'always_render_empty' => true), array('autocomplete' => 'off', 'placeholder' => $form->getDefault('rapidmail_password') ? 'keep old password' : '')));
    $form->setValidator('rapidmail_password', new sfValidatorString(array('max_length' => 100, 'required' => false, 'empty_value' => $form->getDefault('rapidmail_password'))));

    $form->setWidget('rapidmail_recipientlist_id', new sfWidgetFormInputText(['label' => 'Recipientlist ID']));
    $form->setValidator('rapidmail_recipientlist_id', new sfValidatorInteger(array('required' => false)));
  }

  public function checkEnabled(Petition $petition) {
    return $petition->getMailexportData('rapidmail_enabled')
      && $petition->getMailexportData('rapidmail_username')
      && $petition->getMailexportData('rapidmail_password')
      && $petition->getMailexportData('rapidmail_recipientlist_id');
  }

  public function getName() {
    return 'Rapidmail';
  }

  public function test(Petition $petition) {
    $ch = curl_init(self::BASEURL . 'recipientlists' . '/' . $petition->getMailexportData('rapidmail_recipientlist_id'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'policat');
    curl_setopt($ch, CURLOPT_USERPWD, $petition->getMailexportData('rapidmail_username') . ":" . $petition->getMailexportData('rapidmail_password'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json, */*',
      'Content-Type: application/json'
    ]);

    $result=curl_exec($ch);

    if ($result === false) {
      $msg = curl_error($ch);
      @curl_close($ch);
      return ['status' => false, 'message' => 'connection error'];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $array = json_decode($result, true);
    if (!is_array($array)) {
      return ['status' => false, 'message' => 'unexpected result'];
    }

    if ($http_code != 200) {
      return ['status' => false, 'message' => 'unexpected status (' . $http_code . ')'];
    }

    return ['status' => true, 'message' => 'connection works'];
  }
}