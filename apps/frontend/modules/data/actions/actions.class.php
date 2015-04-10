<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
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
      return $right === false ? $this->notFound() : $this->noAccess();
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
      return $right === false ? $this->notFound() : $this->noAccess();
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
      return $right === false ? $this->notFound() : $this->noAccess('You are not Data-owner of this widget.');
    }
  }

  public function executePager(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    $type = isset($route_params['type']) ? $route_params['type'] : null;
    $page = $request->getParameter('page');

    switch ($type) {
      case 'petition':
        $petition = $this->getPetition($request->getParameter('id'));
        if (!$petition instanceof Petition) {
          return $petition;
        }

        return $this->ajax()->replaceWithComponent('#data', 'data', 'list', array('petition' => $petition, 'page' => $page, 'no_filter' => true))->render();

      case 'campaign':
        $campaign = $this->getCampaign($request->getParameter('id'));
        if (!$campaign instanceof Campaign) {
          return $campaign;
        }

        return $this->ajax()->replaceWithComponent('#data', 'data', 'list', array('campaign' => $campaign, 'page' => $page, 'no_filter' => true))->render();

      case 'widget': // this is for widget owners only
        $widget = $this->getWidget($request->getParameter('id'));
        if (!$widget instanceof Widget) {
          return $widget;
        }

        return $this->ajax()->replaceWithComponent('#data', 'data', 'list', array('widget' => $widget, 'page' => $page, 'no_filter' => true))->render();

      default:
        return $this->renderText('error');
    }
  }

  private function buildQueryByRequest(sfWebRequest $request, &$download) {
    $route_params = $this->getRoute()->getParameters();
    $type = isset($route_params['type']) ? $route_params['type'] : null;

    $query = $this->buildQuery($type, $request->getParameter('id'), $this->getRequest()->getGetParameter('_'), $form, $object);

    if ($query instanceof Doctrine_Query && $download instanceof Download) {
      $download->setType($type);
      $download->setSubscriber($form->getSubscriber());
      $download->setFilter(json_encode($form->getValues()));
      $download->setCount($query->count());
      $download->setUser($this->getGuardUser());

      $download->setPages(UtilExport::pages($download->getCount()));
      $download->setPagesProcessed(0);
      $download->setFilename(mt_rand());
      $download->save();
      $download->setFilename($download->getId() . '_' . $this->getGuardUser()->getId() . '_' . $download->getType() . '_' . $object->getId() . '_' . time() . '.csv');

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

      $download->save();
    }

    return $query;
  }

  private function buildQuery($type, $id, $form_data, &$form, &$object) {
    switch ($type) {
      case 'petition':
        $petition = $this->getPetition($id);
        if (!$petition instanceof Petition) {
          return $petition;
        }

        $query = PetitionSigningTable::getInstance()->query(array(PetitionSigningTable::PETITION => $petition));

        // filter
        $form = new SigningsDownloadForm(array(), array(
            SigningsDownloadForm::OPTION_QUERY => $query->copy(),
            SigningsDownloadForm::OPTION_IS_DATA_OWNER => $this->getGuardUser()->isDataOwnerOfCampaign($petition->getCampaign())
        ));
        $form->bind($form_data);
        if ($form->isValid()) {
          $query = PetitionSigningTable::getInstance()->query(
            array_merge(array(PetitionSigningTable::PETITION => $petition), $form->getQueryOptions()));
        }

        $object = $petition;
        return $query;

      case 'campaign':
        $campaign = $this->getCampaign($id);
        if (!$campaign instanceof Campaign) {
          return $campaign;
        }

        $query = PetitionSigningTable::getInstance()->query(array(PetitionSigningTable::CAMPAIGN => $campaign));

        // filter
        $form = new SigningsDownloadForm(array(), array(
            SigningsDownloadForm::OPTION_QUERY => $query->copy(),
            SigningsDownloadForm::OPTION_IS_DATA_OWNER => $this->getGuardUser()->isDataOwnerOfCampaign($campaign)
        ));
        $form->bind($form_data);
        if ($form->isValid()) {
          $query = PetitionSigningTable::getInstance()->query(
            array_merge(array(PetitionSigningTable::CAMPAIGN => $campaign), $form->getQueryOptions()));
        }

        $object = $campaign;
        return $query;

      case 'widget': // this is for widget owners only
        $widget = $this->getWidget($id);
        if (!$widget instanceof Widget) {
          return $widget;
        }

        $query = PetitionSigningTable::getInstance()->query(array(
            PetitionSigningTable::WIDGET => $widget,
            PetitionSigningTable::USER => $this->getGuardUser()
        ));

        // filter
        $form = new SigningsDownloadForm(array(), array(
            SigningsDownloadForm::OPTION_QUERY => $query->copy(),
            SigningsDownloadForm::OPTION_IS_DATA_OWNER => true
        ));
        $form->bind($form_data);
        if ($form->isValid()) {
          $query = PetitionSigningTable::getInstance()->query(
            array_merge(array(
              PetitionSigningTable::WIDGET => $widget,
              PetitionSigningTable::USER => $this->getGuardUser()
              ), $form->getQueryOptions()));
        }

        $object = $widget;
        return $query;

      default:
        return $this->notFound();
    }
  }

  public function executeDownload(sfWebRequest $request) {
    $download = new Download();
    $query = $this->buildQueryByRequest($request, $download);
    if (!$query instanceof Doctrine_Query) {
      return $query;
    }

    $this->ajax()
      ->remove('#prepare-download')
      ->appendPartial('body', 'prepare_modal', array(
          'submit' => array('pages' => UtilExport::pages($download->getCount())),
          'count' => $download->getCount(),
          'prepare_route' => 'data_' . $download->getType() . '_prepare',
          'batch' => $download->getId(),
          'id' => $download->getId(),
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

    // buildQuery checks for permissions
    $query = $this->buildQuery($download->getType(), $download->getIdForType(), json_decode($download->getFilter(), true));
    if (!$query instanceof Doctrine_Query) {
      return $this->renderJson(array('status' => 'fail'));
    }

    $page = (int) $request->getParameter('page', 0);
    if ($page != $download->getPagesProcessed()) {
      if ($page < 0) {
        if ($download->getPagesProcessed() != $download->getPages()) {
          return $this->renderText('error');
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

    UtilExport::writeCsv($download->getFilepath(), $query, $download->getSubscriber(), $page);
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
