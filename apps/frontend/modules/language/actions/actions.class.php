<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * language actions.
 *
 * @package    policat
 * @subpackage store
 * @author     Martin
 */
class languageActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $this->languages = LanguageTable::getInstance()->queryAll()->execute();
  }

  public function executeEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['new'])) {
      $language = new Language();
    } else {
      $language = LanguageTable::getInstance()->find($request->getParameter('id'));

      if (!$language)
        return $this->notFound();
    }

    $this->form = new LanguageForm($language);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->form->save();

        return $this->ajax()->redirectRotue('language_index')->render();
      }
      else
        return $this->ajax()->form($this->form)->render();
    }

    $this->download = false;
    $this->csrf_token = false;
    if (!$language->isNew()) {
      if (file_exists($language->i18nFileWidget())) {
        $this->download = true;
      }

      $this->csrf_token = UtilCSRF::gen('language_upload', $language->getId());
    }
  }

  public function executeDownload(sfWebRequest $request) {
    $language = LanguageTable::getInstance()->find($request->getParameter('id'));

    if (!$language)
      return $this->notFound();

    header('Content-Description: File Transfer');
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=' . '"messages_' . $language->getId() . '.xml"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

    readfile($language->i18nFileWidget());
    exit();
  }

  public function executeUpload(sfWebRequest $request) {
    $language = LanguageTable::getInstance()->find($request->getParameter('id'));
    /* @var $language Language */

    if (!$language)
      return $this->notFound();

    if ($request->getPostParameter('csrf_token') == UtilCSRF::gen('language_upload', $language->getId())) {

      $this->ajax()->setAlertTarget('#upload', 'append');

      $file = $request->getPostParameter('file');
      if ($file) {
        $dir = dirname($language->i18nFileWidget());
        if (!file_exists($dir)) {
          mkdir($dir);
        }
        $validator = new ValidatorUrlEncodedFile(['path' => $dir]);
        try {
          $file = $validator->clean($file);
        } catch (\Exception $e) {
          return $this->ajax()->alert('Upload failed.', '', null, null, false, 'error')->render();
        }

        $file->save('parse.xml.tmp');

        $parser = new sfMessageSource_XLIFF('');
        if ($parser->loadData($dir . '/parse.xml.tmp')) {
          rename($dir . '/parse.xml.tmp', $language->i18nFileWidget());
          $language->i18nCacheWidgetClear();

          return $this->ajax()->alert('Language file updated.', '', null, null, false, 'success')->render();
        }
        @unlink($dir . '/parse.xml.tmp');

        return $this->ajax()->alert('File invalid.', '', null, null, false, 'error')->render();
      }

      return $this->ajax()->alert('Upload failed.', '', null, null, false, 'error')->render();
    }

    return $this->notFound();

  }

}
