<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class dataActions extends policatActions {

  private function rightPetition($petition) {
    if (!$petition) {
      return false;
    }
    if (!$this->userIsAdmin() && $petition->getCampaign()->getStatus() == CampaignTable::STATUS_DELETED) {
      return false;
    }
    if (!$this->getGuardUser()->isPetitionMember($petition, true)) {
      return 0;
    }

    return true;
  }

  /**
   * @param int $id
   * @return Petition
   */
  private function getPetition($id) {
    $petition = PetitionTable::getInstance()->findById($id, $this->userIsAdmin());
    /* @var $petition Petition */

    $right = $this->rightPetition($petition);

    if ($right) {
      return $petition;
    } else {
      return $right === false ? $this->notFound() : $this->noAccess('Access denied.', 'Access denied', true);
    }
  }

  private function rightCampaign($campaign) {
    if (!$campaign) {
      return false;
    }
    if (!$this->getGuardUser()->isCampaignAdmin($campaign)) {
      return 0;
    }

    return true;
  }

  /**
   * @param $id
   * @return Campaign
   */
  private function getCampaign($id) {
    $campaign = CampaignTable::getInstance()->findById($id, $this->userIsAdmin());
    /* @var $campaign Campaign */

    $right = $this->rightCampaign($campaign);

    if ($right) {
      return $campaign;
    } else {
      return $right === false ? $this->notFound() : $this->noAccess('Access denied.', 'Access denied', true);
    }
  }

  private function rightWidget($widget) {
    /* @var $widget Widget */
    if (!$widget) {
      return false;
    }
    if (!$this->userIsAdmin() && $widget->getPetition()->getStatus() == Petition::STATUS_DELETED) {
      return false;
    }
    if (!$this->userIsAdmin() && $widget->getCampaign()->getStatus() == CampaignTable::STATUS_DELETED) {
      return false;
    }
    if (!$widget->isDataOwner($this->getGuardUser())) {
      return 0;
    }

    return true;
  }

  /**
   * @param $id
   * @return Widget
   */
  private function getWidget($id) {
    $widget = WidgetTable::getInstance()->find($id);
    /* @var $widget Widget */

    $right = $this->rightWidget($widget);

    if ($right) {
      return $widget;
    } else {
      return $right === false ? $this->notFound() : $this->noAccess('You are not Data-owner of this widget.', 'Access denied', true);
    }
  }

  public function executePager(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    $type = isset($route_params['type']) ? $route_params['type'] : null;
    $page = $request->getParameter('page');

    $subscriptions = $request->getGetParameter('s') ? true : false;

    switch ($type) {
      case 'petition':
        $petition = $this->getPetition($request->getParameter('id'));
        if (!$petition instanceof Petition) {
          return $petition;
        }

        return $this->ajax()->replaceWithComponent('#data', 'data', 'list', array('petition' => $petition, 'page' => $page, 'subscriptions' => $subscriptions, 'no_filter' => true))->render();

      case 'campaign':
        $campaign = $this->getCampaign($request->getParameter('id'));
        if (!$campaign instanceof Campaign) {
          return $campaign;
        }

        return $this->ajax()->replaceWithComponent('#data', 'data', 'list', array('campaign' => $campaign, 'page' => $page, 'subscriptions' => $subscriptions, 'no_filter' => true))->render();

      case 'widget': // this is for widget owners only
        $widget = $this->getWidget($request->getParameter('id'));
        if (!$widget instanceof Widget) {
          return $widget;
        }

        return $this->ajax()->replaceWithComponent('#data', 'data', 'list', array('widget' => $widget, 'page' => $page, 'subscriptions' => $subscriptions, 'no_filter' => true))->render();

      default:
        return $this->renderText('error');
    }
  }

  /**
   *
   * @param sfWebRequest $request
   * @return \Download
   */
  private function buildDownloadByRequest(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    $type = isset($route_params['type']) ? $route_params['type'] : null;

    $subscriptions = $request->getGetParameter('s') ? true : false;

    $query = $this->buildQuery($type, $request->getParameter('id'), $subscriptions, $this->getRequest()->getGetParameter('_'), $form, $object);
    /* @var $form sfForm */
    if (!$form || !$form->isValid() || !$query) {
      return null;
    }

    $download = new Download();
    $download->setSubscriber($subscriptions ? true : false);
    $download->setUser($this->getGuardUser());

    if ($type === 'petition' && $request->getGetParameter('incremental')) {
      $download->setIncremental(true);
      $download->setType('petition');
      $download->setPetition($object);
      $download->setCampaign($object->getCampaign());
      $download->setFilename(mt_rand());
      $download->save();
      $download->createFilename();
      $download->setQuery(PetitionSigningTable::getInstance()->query(array(
            PetitionSigningTable::DOWNLOAD => $download,
            PetitionSigningTable::PETITION => $object
      )));
      $download->save();
      $rows = PetitionSigningTable::getInstance()->updateByDownload($download);
      $download->setCount($rows);
    } else {
      $download->setType($type);
      $download->setQuery($query);
      $download->setCount($query->count());
    }

    $download->setPages($download->calcPages());
    if ($download->getPages() === 0) {
      if ($download->getId()) {
        $download->delete();
      }
      die('0 pages.');
    }
    $download->setPagesProcessed(0);
    $download->setFilename(mt_rand());
    $download->save();

    switch ($download->getType()) {
      case 'petition':
        $download->setPetition($object);
        $download->setCampaign($object->getCampaign());
        break;
      case 'campaign':
        $download->setCampaign($object);
        break;
      case 'widget':
        $download->setPetition($object->getPetition());
        $download->setCampaign($object->getCampaign());
        $download->setWidget($object);
        break;
    }

    $download->createFilename();
    $download->save();

    return $download;
  }

  private function buildQuery($type, $id, &$subscriptions, $form_data, &$form = null, &$object = null) {
    switch ($type) {
      case 'petition':
        $petition = $this->getPetition($id);
        if (!$petition instanceof Petition) {
          return null;
        }

        // check the rights for subscriptions
        if ($subscriptions && !$this->getGuardUser()->isDataOwnerOfCampaign($petition->getCampaign())) {
          $subscriptions = false;
        }

        $base_query_options = array(
            PetitionSigningTable::PETITION => $petition,
            PetitionSigningTable::SUBSCRIBER => $subscriptions
        );

        // filter
        $form = new SigningsDownloadForm(array(), array(
            PetitionSigningTable::PETITION => $petition,
            SigningsDownloadForm::OPTION_QUERY => PetitionSigningTable::getInstance()->query($base_query_options)
        ));
        $form->bind($form_data);
        if ($form->isValid()) {
          $query = PetitionSigningTable::getInstance()->query(
            array_merge($form->getQueryOptions(), $base_query_options));
        }

        $object = $petition;
        return $query;

      case 'campaign':
        $campaign = $this->getCampaign($id);
        if (!$campaign instanceof Campaign) {
          return null;
        }

        // check the rights for subscriptions
        if ($subscriptions && !$this->getGuardUser()->isDataOwnerOfCampaign($campaign)) {
          $subscriptions = false;
        }

        $base_query_options = array(
            PetitionSigningTable::CAMPAIGN => $campaign,
            PetitionSigningTable::SUBSCRIBER => $subscriptions
        );

        // filter
        $form = new SigningsDownloadForm(array(), array(
            PetitionSigningTable::CAMPAIGN => $campaign,
            SigningsDownloadForm::OPTION_QUERY => PetitionSigningTable::getInstance()->query($base_query_options)
        ));
        $form->bind($form_data);
        if ($form->isValid()) {
          $query = PetitionSigningTable::getInstance()->query(
            array_merge($form->getQueryOptions(), $base_query_options));
        }

        $object = $campaign;
        return $query;

      case 'widget': // this is for widget owners only
        $widget = $this->getWidget($id);
        if (!$widget instanceof Widget) {
          return null;
        }

        $base_query_options = array(
            PetitionSigningTable::WIDGET => $widget,
            PetitionSigningTable::USER => $this->getGuardUser(),
            PetitionSigningTable::SUBSCRIBER => $subscriptions
        );

        // filter
        $form = new SigningsDownloadForm(array(), array(
            SigningsDownloadForm::OPTION_QUERY => PetitionSigningTable::getInstance()->query($base_query_options)
        ));
        $form->bind($form_data);
        if ($form->isValid()) {
          $query = PetitionSigningTable::getInstance()->query(
            array_merge($form->getQueryOptions(), $base_query_options));
        }

        $object = $widget;
        return $query;

      default:
        $this->notFound('Not found.', true);
    }
  }

  public function executeDownload(sfWebRequest $request) {
    $download = $this->buildDownloadByRequest($request);
    if (!$download) {
      return null;
    }

    $this->ajax()
      ->remove('#prepare-download')
      ->appendPartial('body', 'prepare_modal', array(
          'submit' => array('pages' => $download->calcPages()),
          'count' => $download->getCount(),
          'prepare_route' => 'data_' . $download->getType() . '_prepare',
          'batch' => $download->getId(),
          'id' => $download->getId(),
      ))
      ->modal('#prepare-download')
      ->click('#prepare-download .download-prepare');

    return $this->ajax()->render();
  }

  public function executeDownloadIncrement(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    $type = isset($route_params['type']) ? $route_params['type'] : null;
    if ($type !== 'petition') {
      $this->notFound('Not found.', true);
    }

    $petition = $this->getPetition($request->getParameter('id'));

    $download = DownloadTable::getInstance()->createQuery('d')
      ->where('d.petition_id = ?', $request->getParameter('id'))
      ->andWhere('d.id = ?', $request->getParameter('dl'))
      ->fetchOne();

    /* @var $download Download */
    if (!$download) {
      $this->notFound('Not found.', true);
    }

    if ($download->getSubscriber() && !$this->getGuardUser()->isDataOwnerOfCampaign($petition->getCampaign())) {
      $this->forward403();
      return;
    }

    $ready = false;
    if ($download->fileExists()) {
      if ($download->getPages() === $download->getPagesProcessed()) {
        $ready = true;
      } else {
        $download->fileDelete();
      }
    }

    if (!$ready) {
      $download->setCount(PetitionSigningTable::getInstance()->countOldIncrement($download));
      $download->setPages($download->calcPages());
      $download->setPagesProcessed(0);
      $download->save();
    }

    $this->ajax()
      ->remove('#prepare-download')
      ->appendPartial('body', 'prepare_modal', array(
          'submit' => array('pages' => $download->calcPages()),
          'count' => $download->getCount(),
          'prepare_route' => 'data_' . $download->getType() . '_prepare',
          'batch' => $download->getId(),
          'id' => $download->getId(),
          'ready' => $ready
      ))
      ->modal('#prepare-download')
      ->click('#prepare-download .download-prepare');

    return $this->ajax()->render();
  }

  public function executePrepare(sfWebRequest $request) {
    $download = DownloadTable::getInstance()->find((int) $request->getParameter('id'));
    /* @var $download Download */
    if (!$download || $download->getUserId() != $this->getGuardUser()->getId()) {
      return $this->renderJson(array('status' => 'fail'));
    }

    $page = (int) $request->getParameter('page', 0);
    if ($page != $download->getPagesProcessed()) {
      if ($page < 0) {
        if ($download->getPagesProcessed() != $download->getPages()) {
          return $this->renderText('error');
        }

        if (!$download->fileExists()) {
          return $this->forward404();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $download->getDownloadFilename() . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        $out = fopen($download->getFilepath(), 'r');
        fpassthru($out);
        fclose($out);
        flush();

        exit;
      }

      return $this->renderJson(array('status' => 'fail'));
    }

    $download->writeCsv($page);
    $download->setPagesProcessed($page + 1);
    $download->save();
    return $this->renderJson(array('status' => 'ok'));
  }

  public function executeDelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    $signing = PetitionSigningTable::getInstance()->find($id);
    /* @var $signing PetitionSigning */
    if (!$signing) {
      return $this->notFound();
    }

    $user_id = $this->getUser()->getUserId();
    if (!$user_id || $user_id != $signing->getPetition()->getCampaign()->getDataOwnerId()) {
      return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('delete_signing', $signing->getId(), $user_id);

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#signing_delete_modal .modal-body')->render();
      }

      $signing->delete();
      return $this->ajax()->remove('#signing_row_' . $id)->modal('#signing_delete_modal', 'hide')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $id, 'name' => $signing->getComputedName(), 'csrf_token' => $csrf_token))
        ->modal('#signing_delete_modal')
        ->render();
  }

}
