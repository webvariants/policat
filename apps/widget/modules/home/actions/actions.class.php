<?php

/**
 * home actions.
 *
 * @package    policat
 * @subpackage home
 * @author     Martin
 */
class homeActions extends sfActions {

  private function getPetitions() {
    return PetitionTable::getInstance()
        ->createQuery('p')
        ->where('p.status = ?', Petition::STATUS_ACTIVE)
        ->andWhere('p.homepage = 1')
        ->leftJoin('p.PetitionText pt')
        ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
        ->andWhere('p.language_id = pt.language_id')
        ->leftJoin('pt.DefaultWidget w')
        ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
        ->select('p.name, p.object_version, p.kind, p.language_id, p.read_more_url, pt.id, pt.object_version, pt.title, pt.target, pt.body, pt.footer, pt.email_subject, pt.email_body, w.id, w.object_version, w.title, w.target, w.intro, w.footer, w.email_subject, w.email_body')
        ->limit(5)
        ->orderBy('p.created_at DESC, p.id ASC')
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
  }

  public function executeFeed(sfWebRequest $request) {
    $this->setLayout(false);
    $this->getResponse()->setContentType('application/rss+xml');

    $this->petitions = $this->getPetitions();
  }

}
