<?php

class MailExportRapidmail extends MailExport {
  const BASEURL = 'https://apiv3.emailsys.net/v1/';

  public function formSetup(MailExportSettingForm $form) {
    $form->setWidget('rapidmail_enabled', new WidgetBoolean(['label' => 'Enable Rapidmail']));
    $form->setValidator('rapidmail_enabled', new sfValidatorBoolean());
    $form->getWidgetSchema()->setHelp('rapidmail_enabled', 'Once activated, this API submits subscriber data to RapidMail once validated by opt-in email. Note that you have to manually import subscriber data that you collected before you activated this API. Add your RapidMail API account credentials and the ID of the list PoliCAT is supposed to add subscribers to (find the ID in the URL of your RapidMail list). As RapidMail\'s individual API restricts the use of the API to max 10 updates per second, PoliCAT uses the bulk/list option: once every hour, PoliCAT sends all subscriber data validated within that hour. To stop RapidMail sending you an email every time PoliCAT sends an update, you might want to uncheck the "Notification" option in RapidMail. Data includes email, first and last name (or full name), and if applicable, gender and post code. You may define additional fields in your Rapidmail list for signing date, country, widget-ID and URL-referer (the link to the page where the subscriber took part in your action). For OpenECI actions, you might want to track "thank-you-page-shown" as an approximate indicator if a subscriber submitted a statement of support via OpenECI.');

    $form->setWidget('rapidmail_username', new sfWidgetFormInputText(array('label' => 'Username')));
    $form->setValidator('rapidmail_username', new sfValidatorString(array('max_length' => 100, 'required' => false)));

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

  private function curlInit(Petition $petition, $path) {
    $ch = curl_init(self::BASEURL . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'policat');
    curl_setopt($ch, CURLOPT_USERPWD, $petition->getMailexportData('rapidmail_username') . ":" . $petition->getMailexportData('rapidmail_password'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json, */*',
      'Content-Type: application/json'
    ]);

    return $ch;
  }

  public function test(Petition $petition) {
    $ch = $this->curlInit($petition, 'recipientlists' . '/' . $petition->getMailexportData('rapidmail_recipientlist_id'));

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

  public function export(Petition $petition, $verbose = false) {
    $convert_fullname = $petition->getNametype() == Petition::NAMETYPE_FULL;
    $fields = [
      'email' => 'email',
      'firstname' => 'firstname',
      'lastname' => 'lastname'
    ];
    if ($petition->getTitletype() != Petition::TITLETYPE_NO) {
      $fields['gender'] = 'title';
    }
    if ($petition->getWithAddress()) {
      $fields['zip'] = 'post_code';
    }

    $signings = $this->queryPendingSignings($petition)
      ->select('ps.id, ps.email, ps.fullname, ps.firstname, ps.lastname, ps.title, ps.post_code')
      ->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW);

    if (!$signings) {
      return ['status' => true, 'message' => 'no signings, skipping', 'ids' => false];
    }

    if ($verbose) {
      echo "signings: " . count($signings) . "\n";
    }

    $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
    fputcsv($csv, array_keys($fields), ';', '"');

    foreach ($signings as $signing) {
      if ($convert_fullname) {
        $name_parts = explode(' ', $signing['fullname'], 2);
        if (count($name_parts) > 1) {
          $signing['firstname'] = $name_parts[0];
          $signing['lastname'] = $name_parts[1];
        } else {
          $signing['firstname'] = '';
          $signing['lastname'] = $signing['fullname'];
        }
      }

      $row = [];
      foreach ($fields as $field) {
        $row[] = $signing[$field];
      }
      fputcsv($csv, $row, ';', '"');
    }

    rewind($csv);
    $output = stream_get_contents($csv);
    fclose($csv);

    $ids = array_column($signings, 'id');

    return $this->postCSV($petition, $output, $ids, $verbose);
  }

  private function postCSV(Petition $petition, $csv, $ids, $verbose = false) {
    $ch = $this->curlInit($petition, 'recipients/import');
    $body = [
      'recipientlist_id' => (int) $petition->getMailexportData('rapidmail_recipientlist_id'),
      'file' => [
        'content' => base64_encode($csv),
        'type' => 'text/csv'
      ]
    ];

    $json = json_encode($body);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    $result = curl_exec($ch);

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

    if ($http_code != 200 && $http_code != 201) {
      return ['status' => false, 'message' => 'unexpected status (' . $http_code . ')'];
    }

    return ['status' => true, 'message' => 'success', 'ids' => $ids];
  }
}