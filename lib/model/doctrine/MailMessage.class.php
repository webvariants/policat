<?php

/**
 * MailMessage
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class MailMessage extends BaseMailMessage {

  public function setMessagePlain($message) {
    $this->setMessage(base64_encode($message));
  }

  public function getMessagePlain() {
    $message = $this->getMessage();
    if ($message[0] === 'O' && $message[1] === ':') { // already decoded
      return $message;
    }
    return base64_decode($message);
  }

}
