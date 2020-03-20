<?php

class UtilSpf {

  static $STATUS = array(
      1 => 'neutral',
      2 => 'pass',
      3 => 'fail',
      4 => 'softfail',
      5 => 'none',
      6 => 'error',
      7 => 'unknown',
      8 => 'policat error'
  );
  static $STATUS_TEXT = array(
      1 => 'The sender domain explicitly makes no assertion  about  the ip-address. This result must be interpreted exactly as if no SPF record at all existed.',
      2 => 'The ip-address is authorized to send mail for the sender domain.',
      3 => 'The ip-address is unauthorized to send mail for the sender domain.',
      4 => 'The  ip-address  is  not authorized to send mail for the sender domain, but the sender domain cannot or does not wish to make a strong assertion that  no  such mail can ever come from it.',
      5 => 'No SPF record was found.',
      6 => 'A  transient  error occurred (e.g. failure to reach a DNS server), preventing a result from being reached. (temporary)',
      7 => 'One or more SPF records could not be interpreted. (permanent error)',
      8 => ''
  );

  static function query($email) {
    throw \Excetion('deprecated');
    $ip = sfConfig::get('app_spf_ip');
    $cmd = sfConfig::get('app_spf_cmd', 'spfquery');

    if (!$ip || !$cmd) {
      return 8;
    }

    $output = null;
    $status = null;

    exec(escapeshellcmd($cmd) . ' -ip=' . escapeshellarg($ip) . ' -sender' . escapeshellarg($email), $output, $status);

    if (!array_key_exists($status, self::$STATUS)) {
      $status = 8;
    }

    return $status;
  }

}
