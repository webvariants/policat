<?php

/**
 * Petition form base class.
 *
 * @method Petition getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasePetitionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'campaign_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => false)),
      'follow_petition_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FollowPetition'), 'add_empty' => true)),
      'kind'                       => new sfWidgetFormInputText(),
      'titletype'                  => new sfWidgetFormInputText(),
      'nametype'                   => new sfWidgetFormInputText(),
      'status'                     => new sfWidgetFormInputText(),
      'validation_required'        => new sfWidgetFormInputText(),
      'name'                       => new sfWidgetFormTextarea(),
      'addnum'                     => new sfWidgetFormInputText(),
      'addnote'                    => new sfWidgetFormTextarea(),
      'read_more_url'              => new sfWidgetFormTextarea(),
      'landing_url'                => new sfWidgetFormTextarea(),
      'key_visual'                 => new sfWidgetFormInputText(),
      'paypal_email'               => new sfWidgetFormInputText(),
      'donate_url'                 => new sfWidgetFormInputText(),
      'donate_widget_edit'         => new sfWidgetFormInputText(),
      'from_name'                  => new sfWidgetFormInputText(),
      'from_email'                 => new sfWidgetFormInputText(),
      'email_targets'              => new sfWidgetFormTextarea(),
      'homepage'                   => new sfWidgetFormInputText(),
      'twitter_tags'               => new sfWidgetFormInputText(),
      'language_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => true)),
      'mailing_list_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MailingList'), 'add_empty' => true)),
      'editable'                   => new sfWidgetFormInputText(),
      'target_num'                 => new sfWidgetFormInputText(),
      'auto_greeting'              => new sfWidgetFormInputText(),
      'start_at'                   => new sfWidgetFormDate(),
      'end_at'                     => new sfWidgetFormDate(),
      'with_comments'              => new sfWidgetFormInputText(),
      'with_address'               => new sfWidgetFormInputText(),
      'with_country'               => new sfWidgetFormInputText(),
      'with_extra1'                => new sfWidgetFormInputText(),
      'default_country'            => new sfWidgetFormInputText(),
      'subscribe_default'          => new sfWidgetFormInputText(),
      'show_keyvisual'             => new sfWidgetFormInputText(),
      'pledge_with_comments'       => new sfWidgetFormInputText(),
      'pledge_header_visual'       => new sfWidgetFormInputText(),
      'pledge_key_visual'          => new sfWidgetFormInputText(),
      'pledge_background_color'    => new sfWidgetFormInputText(),
      'pledge_color'               => new sfWidgetFormInputText(),
      'pledge_head_color'          => new sfWidgetFormInputText(),
      'pledge_font'                => new sfWidgetFormInputText(),
      'pledge_info_columns'        => new sfWidgetFormTextarea(),
      'activity_at'                => new sfWidgetFormDateTime(),
      'widget_individualise'       => new sfWidgetFormInputText(),
      'style_font_family'          => new sfWidgetFormInputText(),
      'style_title_color'          => new sfWidgetFormInputText(),
      'style_body_color'           => new sfWidgetFormInputText(),
      'style_button_color'         => new sfWidgetFormInputText(),
      'style_bg_left_color'        => new sfWidgetFormInputText(),
      'style_bg_right_color'       => new sfWidgetFormInputText(),
      'style_form_title_color'     => new sfWidgetFormInputText(),
      'style_button_primary_color' => new sfWidgetFormInputText(),
      'style_label_color'          => new sfWidgetFormInputText(),
      'share'                      => new sfWidgetFormInputText(),
      'country_collection_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CountryCollection'), 'add_empty' => true)),
      'deleted_pendings'           => new sfWidgetFormInputText(),
      'label_mode'                 => new sfWidgetFormInputText(),
      'policy_checkbox'            => new sfWidgetFormInputText(),
      'thank_you_email'            => new sfWidgetFormInputText(),
      'themeId'                    => new sfWidgetFormInputText(),
      'last_signings'              => new sfWidgetFormInputText(),
      'created_at'                 => new sfWidgetFormDateTime(),
      'updated_at'                 => new sfWidgetFormDateTime(),
      'object_version'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'campaign_id'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id')),
      'follow_petition_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('FollowPetition'), 'column' => 'id', 'required' => false)),
      'kind'                       => new sfValidatorInteger(array('required' => false)),
      'titletype'                  => new sfValidatorInteger(array('required' => false)),
      'nametype'                   => new sfValidatorInteger(array('required' => false)),
      'status'                     => new sfValidatorInteger(array('required' => false)),
      'validation_required'        => new sfValidatorInteger(array('required' => false)),
      'name'                       => new sfValidatorString(),
      'addnum'                     => new sfValidatorInteger(array('required' => false)),
      'addnote'                    => new sfValidatorString(array('required' => false)),
      'read_more_url'              => new sfValidatorString(array('required' => false)),
      'landing_url'                => new sfValidatorString(array('required' => false)),
      'key_visual'                 => new sfValidatorString(array('max_length' => 60, 'required' => false)),
      'paypal_email'               => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'donate_url'                 => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'donate_widget_edit'         => new sfValidatorInteger(array('required' => false)),
      'from_name'                  => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'from_email'                 => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'email_targets'              => new sfValidatorString(array('required' => false)),
      'homepage'                   => new sfValidatorInteger(array('required' => false)),
      'twitter_tags'               => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'language_id'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id', 'required' => false)),
      'mailing_list_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('MailingList'), 'column' => 'id', 'required' => false)),
      'editable'                   => new sfValidatorInteger(array('required' => false)),
      'target_num'                 => new sfValidatorInteger(array('required' => false)),
      'auto_greeting'              => new sfValidatorInteger(array('required' => false)),
      'start_at'                   => new sfValidatorDate(array('required' => false)),
      'end_at'                     => new sfValidatorDate(array('required' => false)),
      'with_comments'              => new sfValidatorInteger(array('required' => false)),
      'with_address'               => new sfValidatorInteger(array('required' => false)),
      'with_country'               => new sfValidatorInteger(array('required' => false)),
      'with_extra1'                => new sfValidatorInteger(array('required' => false)),
      'default_country'            => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'subscribe_default'          => new sfValidatorInteger(array('required' => false)),
      'show_keyvisual'             => new sfValidatorInteger(array('required' => false)),
      'pledge_with_comments'       => new sfValidatorInteger(array('required' => false)),
      'pledge_header_visual'       => new sfValidatorString(array('max_length' => 60, 'required' => false)),
      'pledge_key_visual'          => new sfValidatorString(array('max_length' => 60, 'required' => false)),
      'pledge_background_color'    => new sfValidatorString(array('max_length' => 6, 'required' => false)),
      'pledge_color'               => new sfValidatorString(array('max_length' => 6, 'required' => false)),
      'pledge_head_color'          => new sfValidatorString(array('max_length' => 6, 'required' => false)),
      'pledge_font'                => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'pledge_info_columns'        => new sfValidatorString(array('required' => false)),
      'activity_at'                => new sfValidatorDateTime(array('required' => false)),
      'widget_individualise'       => new sfValidatorInteger(array('required' => false)),
      'style_font_family'          => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'style_title_color'          => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'style_body_color'           => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'style_button_color'         => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'style_bg_left_color'        => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'style_bg_right_color'       => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'style_form_title_color'     => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'style_button_primary_color' => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'style_label_color'          => new sfValidatorString(array('max_length' => 7, 'required' => false)),
      'share'                      => new sfValidatorInteger(array('required' => false)),
      'country_collection_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CountryCollection'), 'column' => 'id', 'required' => false)),
      'deleted_pendings'           => new sfValidatorInteger(array('required' => false)),
      'label_mode'                 => new sfValidatorInteger(array('required' => false)),
      'policy_checkbox'            => new sfValidatorInteger(array('required' => false)),
      'thank_you_email'            => new sfValidatorInteger(array('required' => false)),
      'themeId'                    => new sfValidatorInteger(array('required' => false)),
      'last_signings'              => new sfValidatorInteger(array('required' => false)),
      'created_at'                 => new sfValidatorDateTime(),
      'updated_at'                 => new sfValidatorDateTime(),
      'object_version'             => new sfValidatorString(array('max_length' => 15, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('petition[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Petition';
  }

}
