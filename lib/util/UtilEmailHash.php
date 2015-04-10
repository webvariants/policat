<?php

class UtilEmailHash {

  const SIMPLE_SALT = 'POLICAT1234567890ABCDE';

  public static function hash($email) {
    $email_trim = trim($email);
    $email_low = mb_strtolower($email_trim, 'UTF-8');

    if (strpos(PHP_VERSION, '5.3.3-') === 0) {
      return md5($email);
    }

    $hash = password_hash($email_low, PASSWORD_BCRYPT, array(
        'salt' => self::SIMPLE_SALT,
        'cost' => 10
    ));

    return $hash;
  }

  public static function test() {
    $hash1 = self::hash('hash-test@policat.org');
    $hash2 = self::hash('  HASH-test@policat.ORG   ');
    $hash_test = '$2y$10$POLICAT1234567890ABCD.KVZJzo7fX5m7gFZO2fS8/bA.LKU.83K';

    $ok = ($hash1 === $hash_test) && (($hash2 === $hash_test));

    return $ok;
  }

}
