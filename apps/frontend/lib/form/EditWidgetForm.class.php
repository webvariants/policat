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
 * Widget form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 */
class EditWidgetForm extends WidgetForm {

  protected $state_count = true;

  public function getStateCount() {
    return $this->state_count;
  }

  public function configure() {
    $this->getWidgetSchema()->setFormFormatterName('bootstrap');
    $this->getWidgetSchema()->setNameFormat('edit_widget[%s]');
    $petition = $this->getObject()->getPetition();
    
    if ($this->isNew()) {
      $this->setWidget('id', new sfWidgetFormInput(array(), array('size' => 4)));
      $this->setValidator('id', new ValidatorFreeId(array('required' => false, ValidatorFreeId::OPTION_MODEL => $this->getModelName())));
    } else {
      $this->setWidget('updated_at', new sfWidgetFormInputHidden());
      $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $this->getObject()->getUpdatedAt())));
    }

    $parent = $this->getObject()->getParentId() ? $this->getObject()->getParent() : null;

    $this->setWidget('title', new sfWidgetFormInput(array(), array('size' => 90, 'class' => 'large')));

    $this->setWidget('styling_type', new sfWidgetFormChoice(array('choices' => array('popup' => 'Popup', 'embed' => 'Embed')), array(
        'class' => 'add_popover',
        'data-content' => 'Choose \'Embed\' to have this box ("widget") embedded into your webpage, including texts and action-form. Visitors can instantly read all and take action. However, you need at least 440px width to embed the widget. Choose \'Popup\' if you lack sufficient space on your webpage. You will get a small box ("teaser") with flexible width (at least 150px). If visitors click on the teaser, the big action-widget pops up.'
    )));
    $this->setValidator('styling_type', new sfValidatorChoice(array('choices' => array('popup', 'embed'))));
    $this->setDefault('styling_type', $this->getObject()->getStyling('type', 'embed'));
    $this->getWidgetSchema()->setLabel('styling_type', 'Widget type');

    $choices = $this->getWidthChoices();
    $this->setWidget('styling_width', new sfWidgetFormChoice(array('choices' => $choices), array(
        'class' => 'add_popover',
        'data-content' => 'You may define a precise widget width. Select "auto" and the widget will adapt to the space available (max: 1000px). Should there be less than 440px width available, contents will display in one column (instead of two) with the sign-on-form below the petition text. On mobile devices with less than 768px device-width, the widget-width is set to 360px for smooth reading on smartphones.'
    )));
    $this->setValidator('styling_width', new sfValidatorChoice(array('choices' => array_keys($choices))));
    $this->setDefault('styling_width', $this->getObject()->getStyling('width', $parent ? $parent->getStyling('width') : 'auto'));
    $this->getWidgetSchema()->setLabel('styling_width', 'Width');

    if ($petition->getWidgetIndividualiseDesign()) {
      $this->setWidget('styling_title_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_title_color', $this->getObject()->getStyling('title_color', $parent ? $parent->getStyling('title_color') : $petition->getStyleTitleColor()));
      $this->getWidgetSchema()->setLabel('styling_title_color', 'Title/Kicker');

      $this->setWidget('styling_body_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_body_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_body_color', $this->getObject()->getStyling('body_color', $parent ? $parent->getStyling('body_color') : $petition->getStyleBodyColor()));
      $this->getWidgetSchema()->setLabel('styling_body_color', 'Content box');

      $this->setWidget('styling_bg_left_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_bg_left_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_left_color', $this->getObject()->getStyling('bg_left_color', $parent ? $parent->getStyling('bg_left_color') : $petition->getStyleBgLeftColor()));
      $this->getWidgetSchema()->setLabel('styling_bg_left_color', 'Context box background');

      $this->setWidget('styling_bg_right_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_bg_right_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_right_color', $this->getObject()->getStyling('bg_right_color', $parent ? $parent->getStyling('bg_right_color') : $petition->getStyleBgRightColor()));
      $this->getWidgetSchema()->setLabel('styling_bg_right_color', 'Widget background');

      $this->setWidget('styling_form_title_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_form_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_form_title_color', $this->getObject()->getStyling('form_title_color', $parent ? $parent->getStyling('form_title_color') : $petition->getStyleFormTitleColor()));
      $this->getWidgetSchema()->setLabel('styling_form_title_color', 'Headings');

      $this->setWidget('styling_button_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_button_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_button_color', $this->getObject()->getStyling('button_color', $parent ? $parent->getStyling('button_color') : $petition->getStyleButtonColor()));
      $this->getWidgetSchema()->setLabel('styling_button_color', 'Other buttons and visual elements');

      $this->setWidget('styling_button_primary_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_button_primary_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_button_primary_color', $this->getObject()->getStyling('button_primary_color', $parent ? $parent->getStyling('button_primary_color') : $petition->getStyleButtonPrimaryColor()));
      $this->getWidgetSchema()->setLabel('styling_button_primary_color', 'Sign button');

      $this->setWidget('styling_label_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_label_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_label_color', $this->getObject()->getStyling('label_color', $parent ? $parent->getStyling('label_color') : $petition->getStyleLabelColor()));
      $this->getWidgetSchema()->setLabel('styling_label_color', 'Other texts and labels');

      $this->setWidget('styling_font_family', new sfWidgetFormChoice(array('choices' => UtilFont::formOptions('default'), 'label' => 'Font')));
      $this->setValidator('styling_font_family', new sfValidatorChoice(array('choices' => UtilFont::$FONTS, 'required' => false)));
      $this->setDefault('styling_font_family', $this->getObject()->getStyling('font_family', ''));
    }

    $this->setWidget('target', new sfWidgetFormTextarea(array('label' => 'Subtitle'), array('cols' => 90, 'rows' => 3, 'class' => 'markdown')));
    $this->getWidgetSchema()->setHelp('target', 'Keep this short, this area is not scrollable.');

    $this->setWidget('background', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5, 'class' => 'markdown')));
    if (!$petition->isEmailKind()) {
      $this->setWidget('intro', new sfWidgetFormTextarea(array('label' => 'Introductory part'), array('cols' => 90, 'rows' => 5, 'class' => 'large')));
      $this->setWidget('footer', new sfWidgetFormTextarea(array('label' => 'Closing part'), array('cols' => 90, 'rows' => 5, 'class' => 'large')));
      $this->getValidator('intro')->setOption('required', false);
      $this->getValidator('footer')->setOption('required', false);
      unset($this['email_subject'], $this['email_body']);
    } else {
      if ($petition->getKind() == Petition::KIND_PLEDGE) {
        unset($this['email_subject'], $this['email_body']);
      } else {
        $this->setWidget('email_subject', new sfWidgetFormInput(array(), array('size' => 90, 'class' => 'large')));
        $this->setWidget('email_body', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5, 'class' => 'large elastic highlight')));
        $this->getValidator('email_subject')->setOption('required', true);
        $this->getValidator('email_body')->setOption('required', true);
      }
      unset($this['intro'], $this['footer']);
      if ($petition->getKind() != Petition::KIND_PLEDGE) {
        $this->getWidgetSchema()->setHelp('email_body', 'You can use the following keywords: ' . $this->getKeywords(', ') . '.');
      }
    }

    $this->setWidgetDefaults();

    $possible_statuses = Widget::$STATUS_SHOW;
    unset($possible_statuses[Widget::STATUS_DRAFT]);
    $possible_statuses = array_keys($possible_statuses);

    $this->state_count = count($possible_statuses);
    $possible_statuses_show = Widget::calcStatusShow($possible_statuses);
    $this->setWidget('status', new sfWidgetFormChoice(array('choices' => $possible_statuses_show)));
    $this->setValidator('status', new sfValidatorChoice(array('choices' => $possible_statuses, 'required' => true)));

    $this->setWidget('landing_url', new sfWidgetFormInput(array(
        'label' => 'E-mail Validation Landingpage - auto forwarding to external page',
        'default' => $this->getObject()->getInheritLandingUrl()
      ), array(
        'size' => 90,
        'class' => 'add_popover large',
        'data-content' => 'Enter URL of external landing page, including \'http://\'. Leave empty for standard landing page',
    )));
    $this->setValidator('landing_url', new ValidatorUrl(array('required' => false, 'trim' => true)));

    $this->removeWidgetIndividualiseFields();

    // donate_url on petition enables/disabled donate_url and donate_text feature
    if ($petition->getDonateUrl() && $petition->getDonateWidgetEdit()) {
      $placeholder = $this->getObject()->getPetitionText()->getDonateUrl();
      if (!$placeholder) {
        $placeholder = $petition->getDonateUrl();
      }
      if (!$placeholder) {
        $placeholder = 'https://www.example.com/donate/';
      }

      $this->setWidget('donate_url', new sfWidgetFormInput(array('label' => 'Link for donation button'), array(
          'size' => 90,
          'class' => 'add_popover large',
          'data-content' => 'Enter the link (URL) to your widget-specific donation page (e.g. "https://" or "http://"). Optional.',
          'placeholder' => $placeholder,
      )));
      $this->setValidator('donate_url', new ValidatorUrl(array('required' => false)));

      $this->setWidget('donate_text', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 3, 'class' => 'markdown')));
      $this->setValidator('donate_text', new sfValidatorString(array('max_length' => 1800, 'required' => false)));
      $this->getWidgetSchema()->setHelp('donate_text', 'This may contain explanatory text and will be displayed in the widget, on click of the \'Donate\' button. It necessitates two clicks to open the donation page in a new browser tab. For a single click workflow, leave this field empty.');

      if ($this->getObject()->isNew()) {
        $this->getWidgetSchema()->setDefault('donate_text', $this->getObject()->getPetitionText()->getDonateText());
      }
    }
  }

}
