<?php

/**
 * @method myUser   getUser()         get the User Object
 * @method null     setContentTags(mixed $tags)
 * @method null     addContentTags(mixed $tags)
 * @method array    getContentTags()
 * @method null     removeContentTags()
 * @method null     setContentTag(string $tagName, string $tagVersion)
 * @method boolean  hasContentTag(string $tagName)
 * @method null     removeContentTag(string $tagName)
 */
class policatComponents extends sfComponents {

  /**
   *
   * @return sfGuardUser
   */
  public function getGuardUser() {
    return $this->getUser()->getGuardUser();
  }

  public function userIsAdmin() {
    return $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
  }

}
