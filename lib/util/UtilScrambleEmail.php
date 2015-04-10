<?php

class UtilScrambleEmail {

  public static function scramble($email) {
    $parts = explode('@', $email);

    if (count($parts) !== 2) {
      return $email;
    }

    return self::scrambleEnd($parts[0], 0.55) . '@' . self::scrambleEnd($parts[1], 0.33);
  }

  private static function scrambleEnd($string, $fraction = 0.3, $char = 'x') {
    $length = mb_strlen($string, 'UTF-8');
    $pos = floor($fraction * $length);
    if ($pos < 1) {
      $pos = 1;
    }

    return mb_substr($string, 0, $pos, 'UTF-8') . preg_replace('/[^.-_]/', $char, mb_substr($string, $pos, $length, 'UTF-8'));
  }

}
