<?php

class facebookActions extends policatActions {
  const HASH_TIME = 1800;

  public function getFormHash($id, $back = 0) {
    if (!is_scalar($id)) $id = '';
    return sha1($id . sfConfig::get('app_facebook_form_hash') . (ceil(time() / self::HASH_TIME) - $back));
  }

  public function checkFormHash($id, $hash) {
    return $hash == $this->getFormHash($id, 0) || $hash == $this->getFormHash($id, 1);
  }

  /**
   *
   * @return Facebook
   */
  protected function getFacebook() {
    require_once sfConfig::get('sf_lib_dir') . '/vendor/facebook/Facebook.class.php';
    return new Facebook(array(
        'appId' => sfConfig::get('app_facebook_appId'),
        'secret' => sfConfig::get('app_facebook_secret'),
        'cookie' => true,
        'domain' => sfConfig::get('app_facebook_domain')
    ));
  }

  public function executeCanvas(sfWebRequest $request) {
    $this->setLayout('facebook');
  }

  public function fetchTabsForPage($cached = true) {
    $cache_id = 'facebook_tab_' . $this->page_id;
    $fb_tabs = null;
    if ($cached) {
      $cache = $this->getCache();
      if ($cache) {
        $fb_tabs = $this->getCache()->get($cache_id, null);
      }
    }
    if ($fb_tabs === null) {
      $fb_tabs = Doctrine_Core::getTable('FacebookTab')->createQuery('ft')
        ->where('page_id = ?', $this->page_id)
        ->addFrom('ft.Language l')
        ->fetchArray();
      $cache = $this->getCache();
      if ($cache) $cache->set($cache_id, $fb_tabs);
    }

    $this->widgets = array();
    foreach ($fb_tabs as $fb_tab)
      $this->widgets[$fb_tab['Language']['name']] = $fb_tab['widget_id'];

    $this->widget_id = null;
    foreach ($fb_tabs as $fb_tab) {
      if ($fb_tab['language_id'] == $this->lang) {
        $this->widget_id = $fb_tab['widget_id'];
        break;
      }
      if ($fb_tab['language_id'] == 'en')
        $this->widget_id = $fb_tab['widget_id'];
    }

    return $fb_tabs;
  }

  public function tabs_added(sfWebRequest $request) {
    $this->setTemplate('tabs_added');
  }

  public function executeTab(sfWebRequest $request) {
    if ($request->hasParameter('tabs_added')) {
      return $this->tabs_added($request);
    }

    $this->setLayout('facebook');
    $this->page_id = null;
    $this->admin = false;
    $this->lang = 'en';
    try {
      $facebook = $this->getFacebook();
      if ($facebook) {
        $signed_request = $facebook->getSignedRequest();
        if ($signed_request) {
          $this->page_id = $signed_request['page']['id'];
          $this->admin = $signed_request['page']['admin'];
          $this->lang = $signed_request['user']['locale'];
          $this->lang = substr($this->lang, 0, 2);
        }
      }
    } catch (Exception $e) {

    }

    // no facebook request but secured by hash
    if (!$this->page_id && $request->hasParameter('secret_hash') && $request->hasParameter('page_id')) {
      $this->page_id = $request->getParameter('page_id');
      if ($this->checkFormHash($this->page_id, $request->getParameter('secret_hash')))
        $this->admin = true;
      else
        $this->page_id = null;
    }

    // WE HAVE A PAGE
    if ($this->page_id) {
      $fb_tabs = $this->fetchTabsForPage();

      // ADMIN
      if ($this->admin) {
        $this->secret_hash = $this->getFormHash($this->page_id);
      }

      // ADMIN - FORM HANDLING
      if ($this->admin && $request->isMethod('POST')) {
        $widget_id = $request->getPostParameter('widget_id', null);
        if ($widget_id !== null && is_numeric($widget_id)) {
          $old = false;
          foreach ($fb_tabs as $fb_tab) {
            if ($widget_id == $fb_tab['widget_id'])
              $old = true;
          }
          if (!$old) {
            $widget = Doctrine_Core::getTable('Widget')
              ->createQuery('w')
              ->where('w.id = ?', $widget_id)
              ->leftJoin('w.PetitionText pt')
              ->select('w.id, pt.id, pt.language_id')
              ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
              ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

            if ($widget) {
              $old = false;
              foreach ($fb_tabs as $fb_tab) {
                if ($widget['PetitionText']['language_id'] == $fb_tab['language_id']) {
                  $old = true;
                  Doctrine_Query::create()
                    ->update('FacebookTab')
                    ->set('widget_id', $widget_id)
                    ->where('id = ?', $fb_tab['id'])
                    ->execute();
                  $this->fetchTabsForPage(false);
                }
              }
              if (!$old) {
                $tab = new FacebookTab();
                $tab->setPageId($this->page_id);
                $tab->setWidgetId($widget_id);
                $tab->setLanguageId($widget['PetitionText']['language_id']);
                $tab->save();
                $this->fetchTabsForPage(false);
              }
            }
          } else {
            // delete old one
            if ($request->hasParameter('remove')) {
              Doctrine_Core::getTable('FacebookTab')
                ->createQuery('ft')
                ->where('ft.page_id = ?', $this->page_id)
                ->andWhere('ft.widget_id = ?', $widget_id)
                ->execute()
                ->delete();
              $this->fetchTabsForPage(false);
            }
          }
        }
      }
    } else {
      $this->setLayout('layout');
      $this->setTemplate('webpage');
    }
  }

}