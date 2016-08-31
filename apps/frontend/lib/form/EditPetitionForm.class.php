<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class EditPetitionForm extends PetitionFieldsForm {

  const USER = 'user';

  private $has_delete_status = false;

  public function hasDeleteStatus() {
    return $this->has_delete_status;
  }

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('edit_petition[%s]');

    $user = $this->getOption(self::USER, null);
    /* @var $user sfGuardUser */

    unset($this['created_at'], $this['updated_at'], $this['campaign_id'], $this['object_version'], $this['email_targets']);
    unset($this['addnote'], $this['mailing_list_id'], $this['editable'], $this['auto_greeting'], $this['key_visual'], $this['kind']);
    unset($this['language_id'], $this['paypal_email']);
    unset($this['pledge_header_visual'], $this['pledge_key_visual']);
    unset($this['pledge_background_color'], $this['pledge_color'], $this['pledge_head_color'], $this['pledge_font']);
    unset($this['pledge_info_columns'], $this['pledge_with_comments'], $this['activity_at'], $this['deleted_pendings'], $this['deleted_hard_bounces'], $this['deleted_bounces_manually']);
    unset($this['label_mode'], $this['follow_petition_id'], $this['addnum'], $this['target_num']);

    $this->configure_fields();

    $this->setWidget('name', new sfWidgetFormInput(array('label' => 'Action name'), array(
        'size' => 90,
        'class' => 'add_popover',
        'data-content' => 'Give your action a short and memorisable name. It won\'t be shown to your supporters. It\'s only for your and your colleague\'s overview.'
    )));

    $this->setWidget('read_more_url', new sfWidgetFormInput(array('label' => '"Read more" link'), array(
        'size' => 90,
        'class' => 'add_popover large',
        'data-content' => 'Add the URL of your campaign site (if you have a central one), including "https://" or https://www. ".  A "Read more" link will appear underneath your e-action. You may leave this field free.',
        'placeholder' => 'https://www.example.com/info'
    )));
    $this->setValidator('read_more_url', new ValidatorUrl(array('required' => false)));

    $this->setWidget('landing_url', new sfWidgetFormInput(array('label' => 'E-mail Validation Landingpage - auto forwarding to external page'), array(
        'size' => 90,
        'class' => 'add_popover large',
        'data-content' => 'Enter URL of external landing page, including \'https://\'. Leave empty for standard landing page',
        'placeholder' => 'https://www.example.com/thank-you'
    )));
    $this->setValidator('landing_url', new ValidatorUrl(array('required' => false, 'trim' => true)));

    $this->setWidget('key_visual', new sfWidgetFormInputFileEditable(array(
        'file_src' => $this->getObject()->getKeyVisual() ? '/images/keyvisual/' . $this->getObject()->getKeyVisual() : null,
        'is_image' => true,
        'with_delete' => true,
        'template' => '<div>%file%<br />%input%<br /><label><span style="float:left">%delete%&nbsp</span> %delete_label%</label></div>',
        'delete_label' => 'No key visual (delete existing key visual)'
    )));
    $this->setValidator('key_visual', new sfValidatorFile(array(
        'required' => false,
        'mime_categories' => 'web_images',
        'path' => sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'keyvisual'
    )));
    $this->getWidgetSchema()->setHelp('key_visual', 'Your key visual should be a strong visual expression of what your action is about. It should not contain text in case your action will be multi-lingual. Make sure, your image wider than 150 pixel but not bigger than 20kB in size. Note: portrait images will be cropped to square format!');
    $this->setValidator('key_visual_delete', new sfValidatorBoolean(array('required' => false)));

    $this->setWidget('show_keyvisual', new sfWidgetFormChoice(array(
        'choices' => array(0 => 'no', 1 => 'yes'),
        'label' => 'Show key visual in signing widget'
      ), array(
    )));
    $this->setValidator('show_keyvisual', new sfValidatorChoice(array('choices' => array(0, 1), 'required' => true)));

    if (StoreTable::value(StoreTable::DONATIONS_PAYPAL)) {
      $this->setWidget('paypal_email', new sfWidgetFormInput(array(
          'label' => 'Paypal account for donations'
        ), array(
          'size' => 90,
          'class' => 'add_popover',
          'data-content' => 'Add your Paypal ID or Paypal e-mail to ask your activists to support your campaign. A "Donate" link will appear underneath your e-action. If people click it, they are asked to donate a free amount of money via Paypal transaction or credit card payment powered by Paypal. You may leave this field free.'
      )));
      $this->setValidator('paypal_email', new ValidatorEmail(array('required' => false, 'max_length' => 80)));
    }

    $this->setWidget('from_name', new sfWidgetFormInput(array(), array(
        'size' => 90,
        'class' => 'add_popover',
        'data-content' => 'Any activist who wants to support your action will receive a verification e-mail. In order to make their participation count, activists must click on the validation link in their verification e-mail, in order to proof their consent. Otherwise, anyone could sign up to an action in anothers\' name. Please specify the name and e-mail address that should appear as the sender of these e-mails. We recommend, you choose a short name that resembles the action title, and your own e-mail address. Note: you must provide a valid e-mail address to which you have access!'
    )));
    $this->setValidator('from_name', new sfValidatorString(array('required' => true, 'max_length' => 80)));

    $this->setWidget('from_email', new sfWidgetFormInput(array(), array(
        'size' => 90,
        'class' => 'add_popover',
        'data-content' => 'Any activist who wants to support your action will receive a verification e-mail. In order to make their participation count, activists must click on the validation link in their verification e-mail, in order to proof their consent. Otherwise, anyone could sign up to an action in anothers\' name. Please specify the name and e-mail address that should appear as the sender of these e-mails. We recommend, you choose a short name that resembles the action title, and your own e-mail address. Note: you must provide a valid e-mail address to which you have access!',
        'placeholder' => 'john.doe@example.com'
    )));
    $this->setValidator('from_email', new ValidatorEmail(array('required' => true, 'max_length' => 80)));
    if (sfConfig::get('app_spf_ip')) {
      $this->getWidgetSchema()->setHelp('from_email', 'Please check if the e-mail domain server of the e-mail you provided allows PoliCAT to send e-mails with your address. Do not use an address if the SPF check result is "fail". You may use an e-mail address if the SPF check result is "none" or "softfail". However, be aware that in these cases a certain percentage of your validation and action e-mails might be considered spam by some e-mail clients. Ideally, ask your e-mail server admin to add ' . sfConfig::get('app_spf_ip') . ' in their SPF record, sample code: your.mail.domain. 3600 IN TXT "v=spf1 mx ip4:' . sfConfig::get('app_spf_ip') . '/32 -all"');
    } else {
      if (StoreTable::getInstance()->value(StoreTable::EMAIL_FROM_ONLY_VERIFIED)) {
        $this->getWidgetSchema()->setHelp('from_email', 'This address is used as "Reply-To" in emails.');
      }
    }

    $this->setWidget('homepage', new sfWidgetFormChoice(array(
        'label' => 'Feature on e-action portal',
        'choices' => array('0' => 'no', '1' => 'yes')), array(
        'class' => 'add_popover',
        'data-content' => 'Select "yes" to feature your e-action on the homepage of our e-action community. Note that for each translation, you need to create at least one widget (in the translations tab), otherwise your action will not be featured! Disclaimer: our friendly admin will cast her or his meticulous eyes over your action and reserves the right to take your action off the portal homepage. We guess that\'s quite unlikely :-)'
    )));
    $this->setValidator('homepage', new sfValidatorChoice(array('choices' => array('0', '1'))));
    $home = sfContext::getInstance()->getRouting()->generate('homepage', array(), true);
    $this->getWidgetSchema()->setHelp('homepage', "The action will be featured on $home if it has 1 or more participants.");

    $this->setWidget('twitter_tags', new sfWidgetFormInput(array(
        'label' => 'Twitter hashtags'
      ), array(
        'size' => 90,
        'class' => 'add_popover',
        'data-content' => 'Enter your distinctive action tags, preceded by "#". Leave a blank space between tags. Your action tags will be added to standard tweets of your supporters. Tweets including these tags will be featured on the homepage.'
    )));

    $possible_statuses = $this->getObject()->calcPossibleStatusForUser($user);
    if (in_array(Petition::STATUS_DELETED, $possible_statuses))
      $this->has_delete_status = true;
    $this->setWidget('status', new sfWidgetFormChoice(array(
        'choices' => Petition::calcStatusShow($possible_statuses)
      ), array(
        'class' => 'add_popover',
        'data-content' => 'Keep the status on draft as long as you want to play around with the e-action settings in this tab. Note that in order to publish the e-action and create widgets, you must set the status to "active". '
    )));
    $this->setValidator('status', new sfValidatorChoice(array('choices' => $possible_statuses, 'required' => true)));

    $this->setWidget('updated_at', new sfWidgetFormInputHidden());
    $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $this->getObject()->getUpdatedAt())));

    $this->setWidget('start_at', new sfWidgetFormInput(array('type' => 'date'), array(
        'class' => 'add_popover',
        'data-content' => 'Leave empty to go live as of immediate. If you pick a later date, you can nevertheless already create widgets and embed them into other websites. However, widgets will not allow for sign-ons before the start date.'
    )));
    $this->setWidget('end_at', new sfWidgetFormInput(array('type' => 'date'), array(
        'class' => 'add_popover',
        'data-content' => 'Pick an end date for your action. When this date has passed, your widgets will not allow for any more sign-ons. Pick a realistic end date. You may change it at later stage, if necessary.'
    )));

    $fonts = array(
        '"Helvetica Neue",Helvetica,Arial,sans-serif',
        'Georgia, serif', '"Palatino Linotype", "Book Antiqua", Palatino, serif', '"Times New Roman", Times, serif',
        'Arial, Helvetica, sans-serif', '"Arial Black", Gadget, sans-serif', '"Comic Sans MS", cursive, sans-serif',
        'Impact, Charcoal, sans-serif', '"Lucida Sans Unicode", "Lucida Grande", sans-serif', 'Tahoma, Geneva, sans-serif',
        '"Trebuchet MS", Helvetica, sans-serif', 'Verdana, Geneva, sans-serif', '"Courier New", Courier, monospace',
        '"Lucida Console", Monaco, monospace', '"Lucida Sans Unicode", Vardana, Arial'
    );

    if ($this->getObject()->getKind() == Petition::KIND_PETITION) {
      $this->setWidget('validation_required', new sfWidgetFormChoice(array(
          'choices' => array(Petition::VALIDATION_REQUIRED_NO => 'no', Petition::VALIDATION_REQUIRED_YES => 'yes'),
          'label' => 'Validation required to count signing.'
        ), array(
          'class' => 'add_popover',
          'data-content' => 'PoliCAT sends this email to each signee, asking to validate the stated email address. This is to improve the credibility of your data: it confirms that the stated email address exists and belongs to the participant. Sometimes, participants will not click this link (e.g. because they forget or the verification email lands in their spam filter). Thus, we recommend that you count signatures immediately (i.e. select "no"). This will also give you access to non-verified data, and may improve your overall participation count. The verification email always includes an "opt-out" link, allowing participants to revoke their participation and delete their data.',
      )));
      $this->setValidator('validation_required', new sfValidatorChoice(array('choices' => array(Petition::VALIDATION_REQUIRED_NO, Petition::VALIDATION_REQUIRED_YES), 'required' => true)));
    } else {
      unset($this['validation_required']);
    }

    $this->setWidget('thank_you_email', new sfWidgetFormChoice(array(
        'choices' => array(Petition::THANK_YOU_EMAIL_NO => 'no', Petition::THANK_YOU_EMAIL_YES => 'yes'),
        'label' => 'Thank-you email after successful validation.'
      )));
    $this->setValidator('thank_you_email', new sfValidatorChoice(array('choices' => array(Petition::VALIDATION_REQUIRED_NO, Petition::VALIDATION_REQUIRED_YES), 'required' => true)));
    $this->getWidgetSchema()->setHelp('thank_you_email', 'Note that this option generates one additional email per participant which will be charged onto your campaign\'s package.');

    if ($this->getObject()->isEmailKind()) { // EMAIL, GEO, GEO_EXTRA => editable
      $this->setWidget('editable', new sfWidgetFormChoice(array('choices' => Petition::$EDITABLE_SHOW, 'label' => 'Text editable'), array(
          'class' => 'add_popover',
          'data-content' => 'If you enable this, activists can modify the action e-mail text as they wish. We recommend you keep the box ticked and encourage your activists to personalise their action e-mails.'
      )));
      $this->setValidator('editable', new sfValidatorChoice(array('choices' => array_keys(Petition::$EDITABLE_SHOW), 'required' => true)));

      if ($this->getObject()->getKind() == Petition::KIND_EMAIL_ACTION) { // EMAIL  => email_target_email_*, email_target_name_*
        $email_targets_json = $this->getObject()->getEmailTargets();
        if (is_string($email_targets_json))
          $email_targets_json = json_decode($email_targets_json, true);
        $email_targets = array();
        if (is_array($email_targets_json))
          foreach ($email_targets_json as $email => $name)
            $email_targets[] = array('email' => $email, 'name' => $name);

        $labels = array(
            1 => array('Name of recipient', 'E-mail address of recipient'),
            2 => array('Name (2nd, optional)', 'E-mail (2nd, optional)'),
            3 => array('Name (3rd, optional)', 'E-mail (3rd, optional)')
        );

        for ($i = 1; $i <= 3; $i++) {
          $this->setWidget("email_target_email_$i", new sfWidgetFormInputText(array('label' => $labels[$i][1]), array(
              'size' => 90,
              'class' => 'add_popover',
              'data-content' => 'Fill in the e-mail address of your target: the recipient of your e-mail-action.' . ($i > 1 ? ' You may leave this field blank.' : ''),
          )));
          $this->setValidator("email_target_email_$i", new ValidatorEmail(array('max_length' => 80, 'min_length' => 3, 'required' => false, 'trim' => true)));
          if (isset($email_targets[$i - 1]))
            $this->setDefault("email_target_email_$i", $email_targets[$i - 1]['email']);
          $this->setWidget("email_target_name_$i", new sfWidgetFormInputText(array('label' => $labels[$i][0]), array(
              'size' => 90,
              'class' => 'add_popover',
              'data-content' => 'Fill in the full name of your target: the recipient of your e-mail-action.' . ($i > 1 ? ' You may leave this field blank.' : ''),
          )));
          $this->setValidator("email_target_name_$i", new sfValidatorString(array('max_length' => 80, 'min_length' => 3, 'required' => false, 'trim' => true)));
          if (isset($email_targets[$i - 1]))
            $this->setDefault("email_target_name_$i", $email_targets[$i - 1]['name']);
        }
      }

      if ($this->getObject()->getKind() == Petition::KIND_PLEDGE) {
        $this->setWidget('pledge_with_comments', new sfWidgetFormChoice(array(
            'label' => 'Enable comments',
            'choices' => array('0' => 'no', '1' => 'yes')), array(
//            'class' => 'add_popover',
//            'data-content' => ''
        )));
        $this->setValidator('pledge_with_comments', new sfValidatorChoice(array('choices' => array('0', '1'))));

        $this->setWidget('pledge_header_visual', new sfWidgetFormInputFileEditable(array(
            'file_src' => '/images/pledge_header_visual/' . $this->getObject()->getPledgeHeaderVisual(),
            'is_image' => true,
            'with_delete' => false,
            'template' => '<div>%file%<br />%input%<br />%delete% %delete_label%</div>',
            'label' => 'Header visual'
        )));
        $this->getWidgetSchema()->setHelp('pledge_header_visual', 'Width should be 1170px and height about 180px. Keep the file small (<80KB). Compress PNGs with tools like http://optipng.sourceforge.net/');
        $this->setValidator('pledge_header_visual', new sfValidatorFile(array(
            'required' => false,
            'mime_categories' => 'web_images',
            'path' => sfConfig::get('sf_web_dir') . '/images/pledge_header_visual'
        )));
        $this->setWidget('pledge_key_visual', new sfWidgetFormInputFileEditable(array(
            'file_src' => '/images/pledge_key_visual/' . $this->getObject()->getPledgeKeyVisual(),
            'is_image' => true,
            'with_delete' => false,
            'template' => '<div>%file%<br />%input%<br />%delete% %delete_label%</div>',
            'label' => 'Key visual'
        )));
        $this->getWidgetSchema()->setHelp('pledge_key_visual', 'Dimensions should be about 140x140px. Keep the file small (<80KB).');
        $this->setValidator('pledge_key_visual', new sfValidatorFile(array(
            'required' => false,
            'mime_categories' => 'web_images',
            'path' => sfConfig::get('sf_web_dir') . '/images/pledge_key_visual'
        )));

        $this->setWidget('pledge_background_color', new sfWidgetFormInputText(array('label' => 'Background colour'), array('class' => 'color')));
        $this->setValidator('pledge_background_color', new sfValidatorRegex(array('pattern' => '/^[0-9a-f]{6}$/i')));
        $this->setWidget('pledge_color', new sfWidgetFormInputText(array('label' => 'Text colour'), array('class' => 'color')));
        $this->setValidator('pledge_color', new sfValidatorRegex(array('pattern' => '/^[0-9a-f]{6}$/i')));
        $this->setWidget('pledge_head_color', new sfWidgetFormInputText(array('label' => 'Header text colour'), array('class' => 'color')));
        $this->setValidator('pledge_head_color', new sfValidatorRegex(array('pattern' => '/^[0-9a-f]{6}$/i')));
        $this->setWidget('pledge_font', new sfWidgetFormChoice(array('choices' => array_combine($fonts, $fonts), 'label' => 'Font')));
        $this->setValidator('pledge_font', new sfValidatorChoice(array('choices' => $fonts)));

        $info_columns = $this->getObject()->getMailingListId() ? $this->getObject()->getMailingList()->getPledgeColumns() : array('country' => 'Country');
        $this->setWidget('pledge_info_columns_comma', new sfWidgetFormInput(array(
            'type' => 'hidden',
            'default' => $this->getObject()->getPledgeInfoColumnsComma(array_keys($info_columns)),
            'label' => 'Target data displayed in widget'
          ), array(
            'class' => 'no-chosen select2sort',
            'data-tags' => json_encode($info_columns),
            'style' => 'width:220px',
            'data-maximumSelectionSize' => 2
        )));
        $this->setValidator('pledge_info_columns_comma', new sfValidatorString(array('required' => false
        )));
      }
    }

    $this->setWidget('widget_individualise', new sfWidgetFormChoice(array(
        'choices' => PetitionTable::$INDIVIDUALISE,
        'label' => 'Adjustability'
    )));

    $this->setValidator('widget_individualise', new sfValidatorChoice(array(
        'required' => true,
        'choices' => array_keys(PetitionTable::$INDIVIDUALISE)
    )));

    $this->setWidget('style_font_family', new sfWidgetFormChoice(array('choices' => array_combine(UtilFont::$FONTS, UtilFont::$FONTS), 'label' => 'Font')));
    $this->setValidator('style_font_family', new sfValidatorChoice(array('choices' => UtilFont::$FONTS)));

    $this->setWidget('style_title_color', new sfWidgetFormInput(array('label' => 'Title/Kicker'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    $this->setWidget('style_body_color', new sfWidgetFormInput(array('label' => 'Context box'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_body_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    $this->setWidget('style_label_color', new sfWidgetFormInput(array('label' => 'Other texts and labels'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_label_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    $this->setWidget('style_button_color', new sfWidgetFormInput(array('label' => 'Other buttons and visual elements'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_button_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    $this->setWidget('style_button_primary_color', new sfWidgetFormInput(array('label' => 'Sign button'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_button_primary_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    $this->setWidget('style_bg_left_color', new sfWidgetFormInput(array('label' => 'Context box background'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_bg_left_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    $this->setWidget('style_bg_right_color', new sfWidgetFormInput(array('label' => 'Widget background'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_bg_right_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    $this->setWidget('style_form_title_color', new sfWidgetFormInput(array('label' => 'Headings'), array('class' => 'color {hash:true}')));
    $this->setValidator('style_form_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));

    if ($this->getObject()->getKind() == Petition::KIND_PETITION) {
      $this->setWidget('label_mode', new sfWidgetFormChoice(array('choices' => PetitionTable::$LABEL_MODE, 'label' => 'Petition labelling')));
      $this->setValidator('label_mode', new sfValidatorChoice(array('choices' => array_keys(PetitionTable::$LABEL_MODE))));
    }

    $this->setWidget('donate_url', new sfWidgetFormInput(array('label' => 'Alternatively: link to donate page'), array(
        'size' => 90,
        'class' => 'add_popover large',
        'data-content' => 'Enter the link (URL) to our donation page (starting with "https://" or "http://"). This will override a donation-via-Paypal setting. You can specify/add language-specific URLs and add optional texts in the translations of the action.',
        'placeholder' => 'https://www.example.com/donate',
    )));
    $this->setValidator('donate_url', new ValidatorUrl(array('required' => false)));

    $this->setWidget('donate_widget_edit', new sfWidgetFormChoice(array(
        'label' => 'Widget owner can edit donation',
        'choices' => array('0' => 'no', '1' => 'yes')), array(
        'class' => 'add_popover',
        'data-content' => 'Select "yes" to allow widget owners to edit donation text and link.'
    )));
    $this->setValidator('donate_widget_edit', new sfValidatorChoice(array('choices' => array('0', '1'))));

    $this->setWidget('policy_checkbox', new sfWidgetFormChoice(array('choices' => PetitionTable::$POLICY_CHECKBOX, 'label' => 'Add privacy policy checkbox')));
    $this->setValidator('policy_checkbox', new sfValidatorChoice(array('choices' => array_keys(PetitionTable::$POLICY_CHECKBOX))));
    $this->getWidgetSchema()->setHelp('policy_checkbox', 'This adds a checkbox to the sign-up form of your action. Activists have to actively tick this box to prove that they accept your privacy policy before they can sign up.');

    $this->setWidget('subscribe_default', new sfWidgetFormChoice(array('choices' => PetitionTable::$SUBSCRIBE_CHECKBOX_DEFAULT, 'label' => 'Keep-me-posted checkbox preselected')));
    $this->setValidator('subscribe_default', new sfValidatorChoice(array('choices' => array_keys(PetitionTable::$SUBSCRIBE_CHECKBOX_DEFAULT))));
    $this->getWidgetSchema()->setHelp('subscribe_default', 'You might increase your subscription rate, if you keep the checkbox preselected. Make sure to comply with EU and your national data protection legislation.');

    $this->setWidget('themeId', new sfWidgetFormChoice(array('label' => 'Theme', 'choices' => UtilTheme::$THEMES)));
    $this->setValidator('themeId', new sfValidatorChoice(array('required' => false, 'choices' => array_keys(UtilTheme::$THEMES))));

    $this->setWidget('last_signings', new sfWidgetFormChoice(array('choices' => PetitionTable::$LAST_SINGINGS, 'label' => 'Show participants list')));
    $this->setValidator('last_signings', new sfValidatorChoice(array('choices' => array_keys(PetitionTable::$LAST_SINGINGS))));

    $this->setWidget('share', new WidgetFormInputCheckbox(array('value_attribute_value' => '1', 'value_checked' => '1', 'value_unchecked' => '0', 'label' => 'Include share buttons underneath sign-button')));
    $this->setValidator('share', new sfValidatorChoice(array('choices' => array('0', '1'))));
  }

  public function processValues($values) {
    $values = parent::processValues($values);
    if ($this->getObject()->isEmailKind()) {
      if ($this->getObject()->getKind() == Petition::KIND_EMAIL_ACTION) {
        $email_targets = array();
        for ($i = 1; $i <= 3; $i++) {
          $name = $this->getValue("email_target_name_$i");
          $email = $this->getValue("email_target_email_$i");
          if (!empty($email))
            $email_targets[$email] = empty($name) ? '' : $name;
        }
        $values['email_targets'] = json_encode($email_targets);
      }
    }
    return $values;
  }

}
