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

  const USER = 'user';

  use FormTargetSelectorPreselect;

  protected $state_count = true;

  public function getStateCount() {
    return $this->state_count;
  }

  public function configure() {
    $this->getWidgetSchema()->setFormFormatterName('bootstrap');
    $this->getWidgetSchema()->setNameFormat('edit_widget[%s]');
    $petition = $this->getObject()->getPetition();
    $petition_text = $this->getObject()->getPetitionText();

    if (isset($this['paypal_email'])) {
      $this->getWidgetSchema()->setLabel('paypal_email', 'Include donation form');
    }

    if ($this->isNew()) {
      $this->setWidget('id', new sfWidgetFormInput(array(), array('size' => 4)));
      $this->setValidator('id', new ValidatorFreeId(array('required' => false, ValidatorFreeId::OPTION_MODEL => $this->getModelName())));
    } else {
      $this->setWidget('updated_at', new sfWidgetFormInputHidden());
      $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $this->getObject()->getUpdatedAt())));
    }

    $parent = $this->getObject()->getParentId() ? $this->getObject()->getParent() : null;

    $this->setWidget('title', new sfWidgetFormInput(array(), array('size' => 90, 'class' => 'large', 'placeholder' => 'Optional (you may leave this field empty). Add here a short and movtivating action title.')));

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

    if ($petition->getWithCountry()) {
      $culture_info    = sfCultureInfo::getInstance('en');
      if ($petition->getCountryCollectionId()) {
          $countries = $petition->getCountryCollection()->getCountriesList();
      } else {
        $countries_false = array_keys($culture_info->getCountries());
        $countries       = array();
        foreach ($countries_false as $country) {
          if (!is_numeric($country)) {
            $countries[] = $country;
          }
        }
        $countries = array_diff($countries, array('QU', 'ZZ'));
      }

      $this->setWidget('default_country', new sfWidgetFormI18nChoiceCountry(array('countries' => $countries, 'culture' => 'en', 'add_empty' => ''), array('data-placeholder' => 'use action default' . ($petition->getDefaultCountry() ? ' (' . $culture_info->getCountry($petition->getDefaultCountry()) . ')' : ' (no country preselected)'))));
      if ($petition->getDefaultCountry()) {
        $this->setDefault('default_country', $petition->getDefaultCountry());
      }
      $this->setValidator('default_country', new sfValidatorI18nChoiceCountry(array('countries' => $countries, 'required' => false)));
    }

    $this->setWidget('share', new WidgetFormInputCheckbox(array('value_attribute_value' => '1', 'value_checked' => '1', 'value_unchecked' => '0', 'label' => 'Include share buttons underneath sign-button')));
    $this->setValidator('share', new sfValidatorChoice(array('choices' => array('0', '1'))));

    if ($petition->getWidgetIndividualiseDesign()) {
      $this->setWidget('themeId', new sfWidgetFormChoice(array('label' => 'Theme', 'choices' => UtilTheme::themesByKind($petition->getKind()))));
      $this->setValidator('themeId', new sfValidatorChoice(array('required' => false, 'choices' => array_keys(UtilTheme::themesByKind($petition->getKind())))));

      $this->setWidget('styling_title_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_title_color', $this->getObject()->getStyling('title_color', $parent ? $parent->getStyling('title_color') : $petition->getStyleTitleColor()));
      $this->getWidgetSchema()->setLabel('styling_title_color', 'Title/Kicker');

      $this->setWidget('styling_body_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_body_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_body_color', $this->getObject()->getStyling('body_color', $parent ? $parent->getStyling('body_color') : $petition->getStyleBodyColor()));
      $this->getWidgetSchema()->setLabel('styling_body_color', 'Context box');

      $this->setWidget('styling_bg_left_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_bg_left_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_left_color', $this->getObject()->getStyling('bg_left_color', $parent ? $parent->getStyling('bg_left_color') : $petition->getStyleBgLeftColor()));
      $this->getWidgetSchema()->setLabel('styling_bg_left_color', 'Context box background');

      $this->setWidget('styling_bg_right_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_bg_right_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_right_color', $this->getObject()->getStyling('bg_right_color', $parent ? $parent->getStyling('bg_right_color') : $petition->getStyleBgRightColor()));
      $this->getWidgetSchema()->setLabel('styling_bg_right_color', 'Widget background');

      $this->setWidget('styling_form_title_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_form_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_form_title_color', $this->getObject()->getStyling('form_title_color', $parent ? $parent->getStyling('form_title_color') : $petition->getStyleFormTitleColor()));
      $this->getWidgetSchema()->setLabel('styling_form_title_color', 'Headings');

      $this->setWidget('styling_button_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_button_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_button_color', $this->getObject()->getStyling('button_color', $parent ? $parent->getStyling('button_color') : $petition->getStyleButtonColor()));
      $this->getWidgetSchema()->setLabel('styling_button_color', 'Other buttons and visual elements');

      $this->setWidget('styling_button_primary_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_button_primary_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_button_primary_color', $this->getObject()->getStyling('button_primary_color', $parent ? $parent->getStyling('button_primary_color') : $petition->getStyleButtonPrimaryColor()));
      $this->getWidgetSchema()->setLabel('styling_button_primary_color', 'Sign button');

      $this->setWidget('styling_label_color', new sfWidgetFormInput(array(), array('class' => 'jscolor {hash:true}')));
      $this->setValidator('styling_label_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_label_color', $this->getObject()->getStyling('label_color', $parent ? $parent->getStyling('label_color') : $petition->getStyleLabelColor()));
      $this->getWidgetSchema()->setLabel('styling_label_color', 'Other texts and labels');

      $this->setWidget('styling_font_family', new sfWidgetFormChoice(array('choices' => UtilFont::formOptions('default'), 'label' => 'Font')));
      $this->setValidator('styling_font_family', new sfValidatorChoice(array('choices' => UtilFont::$FONTS, 'required' => false)));
      $this->setDefault('styling_font_family', $this->getObject()->getStyling('font_family', ''));
    }

    $mediaMarkupSet = MediaFileTable::getInstance()->dataMarkupSet($petition);

    $this->setWidget('target', new sfWidgetFormTextarea(array('label' => 'Subtitle'), array(
        'cols' => 90,
        'rows' => 3,
        'class' => 'markdown',
        'placeholder' => 'Optional (you may leave this field empty). Add here a short contextual introduction, or name the targets of your action (e.g. "To the heads of states of the European Union". Keep it very short!',
        'data-markup-set-1' => $mediaMarkupSet
    )));

    $this->setWidget('background', new sfWidgetFormTextarea(array(), array(
        'cols' => 90,
        'rows' => 5,
        'class' => 'markdown',
        'placeholder' => 'Optional (you may leave this field empty). Add here further contextual information about this action. You may add external media files (make sure they are hosted on a server with an encrypted SSL connection).',
        'data-markup-set-1' => $mediaMarkupSet
    )));
    if (!$petition->isEmailKind()) {
      $this->setWidget('intro', new sfWidgetFormTextarea(array('label' => 'Introductory part'), array(
          'cols' => 90,
          'rows' => 5,
          'class' => 'markdown',
          'data-markup-set-1' => $mediaMarkupSet
      )));
      $this->setWidget('footer', new sfWidgetFormTextarea(array('label' => 'Closing part'), array(
          'cols' => 90,
          'rows' => 5,
          'class' => 'markdown',
          'data-markup-set-1' => $mediaMarkupSet
      )));
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
    $this->setWidget('status', new sfWidgetFormChoice(array('choices' => $possible_statuses_show), array('class' => 'form-control')));
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

    if ($petition->getKind() == Petition::KIND_OPENECI) {
        $this->setWidget('landing2_url', new sfWidgetFormInput(array(
          'label' => 'Alternative opt-in landing page, if OpenECI form was not submitted',
          'default' => $this->getObject()->getInheritLanding2Url()
        ), array(
            'size' => 90,
            'class' => 'add_popover large',
            'data-content' => 'Provide an alternative landing page, containing the ECI form and a prompt to "support the ECI, if you haven\'t done yet"',
            'placeholder' =>'https://www.example.com/-language-/thank-you'
        )));
        $this->setValidator('landing2_url', new ValidatorUrl(array('required' => false, 'trim' => true)));
    }

    $this->removeWidgetIndividualiseFields();

    // donate_url on petition enables/disabled donate_url and donate_text feature
    if ($petition->getDonateUrl() && $petition->getDonateWidgetEdit()) {
      $placeholder = $petition_text->getDonateUrl();
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
        $this->getWidgetSchema()->setDefault('donate_text', $petition_text->getDonateText());
      }
    }

    $this->configureTargetSelectors();

    $this->setWidget('social_share_text', new sfWidgetFormInput(array('label' => 'Twitter message'), array('size' => 90, 'class' => 'large', 'placeholder' => 'Leave this field empty to use standard texts.')));
    $this->setValidator('social_share_text', new sfValidatorString(array('max_length' => 1000, 'required' => false)));
    $this->getWidgetSchema()->setHelp('social_share_text', 'Optional keywords: #TITLE#, #WIDGET-HEADING#. Keep the text short. URL is appended automatically.');

    $this->setWidget('read_more_url', new sfWidgetFormInput(array('label' => '"Read more" link'), array(
      'size' => 90,
      'class' => 'add_popover large',
      'data-content' => 'Enter the URL of your campaign site for this language, including "https://" or https://www. ". A "Read more" link will appear underneath your e-action. Leave empty for standard "Read more" page.',
      'placeholder' => $petition_text->getReadMoreUrl() ?: ($petition->getReadMoreUrl() ?: 'https://www.example.com/' . $petition_text->getLanguageId() . '/info')
    )));
    $this->setValidator('read_more_url', new ValidatorUrl(array('required' => false)));

    $user = $this->getOption(self::USER, null);
    /* @var $user sfGuardUser */

    if ($this->getObject()->isInDataOwnerMode() && $user && $this->getObject()->getUser()->getId() === $user->getId()) {
      $choices = PetitionTable::$WIDGET_SUBSCRIBE_CHECKBOX_DEFAULT;
      $choices[PetitionTable::SUBSCRIBE_CHECKBOX_INHERIT] = $choices[PetitionTable::SUBSCRIBE_CHECKBOX_INHERIT] . ' [' . PetitionTable::$WIDGET_SUBSCRIBE_CHECKBOX_DEFAULT[$petition->getSubscribeDefault()] .  ']';
      $this->setWidget('subscribe_default', new sfWidgetFormChoice(array('choices' => $choices, 'label' => 'Keep-me-posted checkbox')));
      $this->setValidator('subscribe_default', new sfValidatorChoice(array('choices' => array_keys(PetitionTable::$WIDGET_SUBSCRIBE_CHECKBOX_DEFAULT))));
      $this->getWidgetSchema()->setHelp('subscribe_default', 'You might increase your subscription rate, if you keep the checkbox preselected. However, preselection is not legally in conformity with the EU General Data Protection Regulation. It is your legal obligation to make sure your selection is in conformity with EU and national data protection legislation.');

      if (!$this->getObject()->getSubscribeText()) {
        $this->getObject()->setSubscribeText($petition_text->getSubscribeText());
      }
      $this->setWidget('subscribe_text', new sfWidgetFormInput(array('label' => 'Keep-me-posted checkbox text'), array('size' => 90, 'class' => 'large', 'placeholder' => 'Leave this field empty to use standard texts.')));
      $this->setValidator('subscribe_text', new sfValidatorString(array('max_length' => 250, 'required' => false)));
      $this->getWidgetSchema()->setHelp('subscribe_text', 'You may customise the text of the keep-me-posted checkbox. Leave this field empty to use action or standard texts. You may use the following keywords to include the name or email of the respective data owner: #DATA-OFFICER-NAME#, #DATA-OFFICER-ORGA#, #DATA-OFFICER-EMAIL#');

      if ($petition->getPrivacyPolicyByWidgetDataOwner()) {
        $this->setWidget('privacy_policy_body', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 30, 'class' => 'markdown highlight')));
        if (!$this->getObject()->getPrivacyPolicyBody()) { // if empty get default from petition translation/text
          $this->getWidgetSchema()->setDefault('privacy_policy_body', $petition_text->getPrivacyPolicyBody());
        }
        $this->setValidator('privacy_policy_body', new sfValidatorString(array('required' => false)));
        $this->getWidgetSchema()->setHelp('privacy_policy_body', '#DATA-OFFICER-NAME#, #DATA-OFFICER-ORGA#, #DATA-OFFICER-EMAIL#, #DATA-OFFICER-WEBSITE#, #DATA-OFFICER-PHONE#, #DATA-OFFICER-MOBILE#, #DATA-OFFICER-STREET#, #DATA-OFFICER-POST-CODE#, #DATA-OFFICER-CITY#, #DATA-OFFICER-COUNTRY#, #DATA-OFFICER-ADDRESS#');

        $this->setWidget('privacy_policy_url', new sfWidgetFormInput(array('label' => 'Privacy policy URL'), array(
          'size' => 90,
          'placeholder' => 'https://www.example.com/privacy_policy'
        )));
        $this->setValidator('privacy_policy_url', new ValidatorUrl(array('required' => false)));
        $this->getWidgetSchema()->setHelp('privacy_policy_url', 'Leave this empty to show the privacy policy text as below within the widget (recommended). If a click on "privacy policy" should open your own privacy policy page instead, enter its URL here, including "https://".');

        $this->setWidget('privacy_policy_link_text', new sfWidgetFormInput(['label' => 'Privacy policy link'], ['size' => 90, 'placeholder' => $petition_text->getPrivacyPolicyLinkText() ?: 'I accept the _privacy policy_']));
        $this->setValidator('privacy_policy_link_text', new sfValidatorRegex(['required' => false, 'pattern' => '/^[^_]*_[^_]+_[^_]*$/i'], ['invalid' => 'Missing enclosing underscores.']));
        $this->getWidgetSchema()->setHelp('privacy_policy_link_text', 'You may customise the text of the privacy policy link. Leave this field empty to use standard texts. You may use the following keywords to include the name or email of the respective data owner: #DATA-OFFICER-NAME#, #DATA-OFFICER-ORGA#, #DATA-OFFICER-EMAIL#. Add "_" before and after the word or phrase that should link to your internal or external privacy policy text (required)');
      }

      $this->setWidget('email_validation_subject', new sfWidgetFormInput(array('label' => 'Opt-In Confirmation Email Subject'), array('size' => 90, 'class' => 'large')));
      $this->setValidator('email_validation_subject', new sfValidatorString(array('required' => false)));
      if (!$this->getObject()->getEmailValidationSubject()) { // if empty get default from petition translation/text
        $this->getWidgetSchema()->setDefault('email_validation_subject', $petition_text->getEmailValidationSubject());
      }
      $this->setWidget('email_validation_body', new sfWidgetFormTextarea(array('label' => 'Opt-In Confirmation Email Body'), array(
          'cols' => 90,
          'rows' => 16,
          'class' => 'markdown highlight email-template markItUp-higher',
          'data-markup-set-1' => UtilEmailLinks::dataMarkupSet(array(UtilEmailLinks::VALIDATION, UtilEmailLinks::DISCONFIRMATION, UtilEmailLinks::REFERER, UtilEmailLinks::READMORE)),
          'data-markup-set-2' => $mediaMarkupSet
      )));
      $this->setValidator('email_validation_body', new ValidatorKeywords(array('required' => false, 'keywords' => array('#VALIDATION-URL#'))));
      $this->getWidgetSchema()->setHelp('email_validation_body', '#VALIDATION-URL#, #DISCONFIRMATION-URL#,' . $email_keywords);
      if (!$this->getObject()->getEmailValidationBody()) { // if empty get default from petition translation/text
        $this->getWidgetSchema()->setDefault('email_validation_body', $petition_text->getEmailValidationBody());
      }

      $this->setWidget('from_name', new sfWidgetFormInput(array(), array(
        'size' => 90,
        'class' => 'add_popover',
        'data-content' => 'Any activist who wants to support your action will receive a verification e-mail. In order to make their participation count, activists must click on the validation link in their verification e-mail, in order to proof their consent. Otherwise, anyone could sign up to an action in anothers\' name. Please specify the name and e-mail address that should appear as the sender of these e-mails. We recommend, you choose a short name that resembles the action title, and your own e-mail address. Note: you must provide a valid e-mail address to which you have access!',
        'placeholder' => $this->getObject()->getUser()->getFromNameWithOrganisation()
      )));
      $this->setValidator('from_name', new sfValidatorString(array('required' => true, 'max_length' => 80)));

      $this->setWidget('from_email', new sfWidgetFormInput(array(), array(
          'size' => 90,
          'class' => 'add_popover',
          'data-content' => 'Any activist who wants to support your action will receive a verification e-mail. In order to make their participation count, activists must click on the validation link in their verification e-mail, in order to proof their consent. Otherwise, anyone could sign up to an action in anothers\' name. Please specify the name and e-mail address that should appear as the sender of these e-mails. We recommend, you choose a short name that resembles the action title, and your own e-mail address. Note: you must provide a valid e-mail address to which you have access!',
          'placeholder' => $this->getObject()->getUser()->getEmailAddress()
      )));
      $this->setValidator('from_email', new ValidatorEmail(array('required' => true, 'max_length' => 80)));
      if (UtilMail::fromOnlyVerified()) {
        $verfified = '';
        if ($this->getObject()->getFromEmail() && UtilMail::isVerified($this->getObject()->getFromEmail())) {
          $this->getWidgetSchema()->setHelp('from_email', 'Your Email is verified and set as FROM address for transactional (opt-in, thank-you) emails.');
        } else {
          $this->getWidgetSchema()->setHelp('from_email', 'Transactional (opt-in, thank-you) emails are sent FROM policat@policat.org with your name and the email you provide here as REPLY-TO address. To set your email as FROM address, you have to add SPF/DKIM records to your email server. Contact us for details, using the contact form.');
        }
      }
    }
  }

  public function processValues($values) {
    $values = parent::processValues($values);
    $values = $this->processTargetSelectorValues($values);

    return $values;
  }

}
