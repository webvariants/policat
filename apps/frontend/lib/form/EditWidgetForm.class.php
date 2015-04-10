<?php

/**
 * Widget form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 */
class EditWidgetForm extends BaseWidgetForm {

  protected $state_count = true;

  public function getStateCount() {
    return $this->state_count;
  }

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('edit_widget[%s]');
    $petition = $this->getObject()->getPetition();

    unset
      (
      $this['user_id'], $this['data_owner'], $this['created_at'], $this['updated_at'], $this['campaign_id'],
      $this['petition_id'], $this['petition_text_id'], $this['stylings'], $this['email'], $this['organisation'],
      $this['validation_kind'], $this['validation_data'], $this['validation_status'], $this['edit_code'],
      $this['object_version'], $this['parent_id'], $this['ref'], $this['paypal_email'], $this['activity_at'],
      $this['last_ref']
    );

    $parent = $this->getObject()->getParentId() ? $this->getObject()->getParent() : null;

    if ($this->isNew()) {
      $this->setWidget('id', new sfWidgetFormInput(array(), array('size' => 4)));
      $this->setValidator('id', new ValidatorFreeId(array('required' => false, ValidatorFreeId::OPTION_MODEL => $this->getModelName())));
    }

    $this->setWidget('title', new sfWidgetFormInput(array(), array('size' => 90, 'class' => 'large')));

    $this->setWidget('styling_type', new sfWidgetFormChoice(array('choices' => array('popup' => 'Popup', 'embed' => 'Embed'))));
    $this->setValidator('styling_type', new sfValidatorChoice(array('choices' => array('popup', 'embed'))));
    $this->setDefault('styling_type', $this->getObject()->getStyling('type', 'embed'));
    $this->widgetSchema->setLabel('styling_type', 'Widget type');

    $choices = array('auto' => 'auto');
    for ($i = 440; $i <= 740; $i++)
      $choices[$i] = $i;
    //$this->setWidget('styling_width',    new sfWidgetFormInput());
    $this->setWidget('styling_width', new sfWidgetFormChoice(array('choices' => $choices)));
    $this->setValidator('styling_width', new sfValidatorChoice(array('choices' => array_keys($choices))));
    $this->setDefault('styling_width', $this->getObject()->getStyling('width', $parent ? $parent->getStyling('width') : 'auto'));
    $this->widgetSchema->setLabel('styling_width', 'Width');

    if ($petition->getWidgetIndividualiseDesign()) {
      $this->setWidget('styling_title_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_title_color', $this->getObject()->getStyling('title_color', $parent ? $parent->getStyling('title_color') : $petition->getStyleTitleColor()));
      $this->widgetSchema->setLabel('styling_title_color', 'Text title');

      $this->setWidget('styling_body_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_body_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_body_color', $this->getObject()->getStyling('body_color', $parent ? $parent->getStyling('body_color') : $petition->getStyleBodyColor()));
      $this->widgetSchema->setLabel('styling_body_color', 'Text body');

      $this->setWidget('styling_bg_left_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_bg_left_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_left_color', $this->getObject()->getStyling('bg_left_color', $parent ? $parent->getStyling('bg_left_color') : $petition->getStyleBgLeftColor()));
      $this->widgetSchema->setLabel('styling_bg_left_color', 'Backgr left');

      $this->setWidget('styling_bg_right_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_bg_right_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_right_color', $this->getObject()->getStyling('bg_right_color', $parent ? $parent->getStyling('bg_right_color') : $petition->getStyleBgRightColor()));
      $this->widgetSchema->setLabel('styling_bg_right_color', 'Backgr right');

      $this->setWidget('styling_form_title_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_form_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_form_title_color', $this->getObject()->getStyling('form_title_color', $parent ? $parent->getStyling('form_title_color') : $petition->getStyleFormTitleColor()));
      $this->widgetSchema->setLabel('styling_form_title_color', 'Form title');

      $this->setWidget('styling_button_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_button_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_button_color', $this->getObject()->getStyling('button_color', $parent ? $parent->getStyling('button_color') : $petition->getStyleButtonColor()));
      $this->widgetSchema->setLabel('styling_button_color', 'Button');
    }

    $this->setWidget('target', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 3, 'class' => 'markdown')));
    $this->getWidgetSchema()->setHelp('target', 'Keep this short, this area is not scrollable.');

    $this->setWidget('background', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5, 'class' => 'markdown')));
    if (!$petition->isEmailKind()) {
      $this->setWidget('intro', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5, 'class' => 'large')));
      $this->setWidget('footer', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5, 'class' => 'large')));
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
        if ($petition->isGeoKind()) {
          $subst_fields = $petition->getGeoSubstFields();
          $keywords = array();
          foreach ($subst_fields as $keyword => $subst_field) {
            if ($subst_field['id'] != MailingList::FIX_GENDER) {
              $keywords[] = '<b>' . $keyword . '</b> (' . $subst_field['name'] . ')';
            }
          }
          foreach (PetitionSigningTable::$KEYWORDS as $keyword) {
            $keywords[] = $keyword;
          }

          $keywords[] = PetitionTable::KEYWORD_PERSONAL_SALUTATION;
        } else {
          $keywords = PetitionSigningTable::$KEYWORDS;
        }
        $this->getWidgetSchema()->setHelp('email_body', 'You can use the following keywords: ' . implode(', ', $keywords) . '.');
      }
    }

    $defaults = $this->getObject()->getPetitionText();
    if ($this->getObject()->getParentId())
      $defaults = $this->getObject()->getParent();
    if ($this->getObject()->isNew())
      foreach (array('title', 'target', 'background', 'intro', 'footer', 'email_subject', 'email_body') as $field)
        if (isset($this[$field]))
          $this->setDefault($field, $defaults[$field]);

    $possible_statuses = Widget::$STATUS_SHOW;
    unset($possible_statuses[Widget::STATUS_DRAFT]);
    $possible_statuses = array_keys($possible_statuses);

    $this->state_count = count($possible_statuses);
    $possible_statuses_show = Petition::calcStatusShow($possible_statuses);
    $this->setWidget('status', new sfWidgetFormChoice(array('choices' => $possible_statuses_show)));
    $this->setValidator('status', new sfValidatorChoice(array('choices' => $possible_statuses, 'required' => true)));
//    }
//    else
//    {
//      $this->getObject()->setStatus(Widget::STATUS_ACTIVE);
//      unset($this['status']);
//    }

    $petition_paypal_email = $petition->getPaypalEmail();
    if ((is_string($petition_paypal_email) && strpos($petition_paypal_email, '@')) || !$this instanceof WidgetPublicForm) {
      $this->setWidget('paypal_email', new WidgetFormInputInverseCheckbox(array('value_attribute_value' => 'ignore')));
      $this->setValidator('paypal_email', new ValidatorInverseCheckbox(array('value_attribute_value' => 'ignore')));
      $this->getWidgetSchema()->setLabel('paypal_email', 'Include fundraising form');
    }

    if (!$this->getObject()->isNew()) {
      $this->setWidget('updated_at', new sfWidgetFormInputHidden());
      $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $this->getObject()->getUpdatedAt())));
    }

    $this->setWidget('landing_url', new sfWidgetFormInput(array(
        'label' => 'Email Validation Landingpage - auto forwarding to external page',
        'default' => $this->getObject()->getInheritLandingUrl()
      ), array(
        'size' => 90,
        'class' => 'add_popover large',
        'data-content' => 'Enter URL of external landing page, including \'http://\'. Leave empty for standard landing page',
    )));
    $this->setValidator('landing_url', new ValidatorUrl(array('required' => false, 'trim' => true)));

    if (!$petition->getWidgetIndividualiseText()) {
      foreach (array('title', 'target', 'background', 'intro', 'footer', 'email_subject', 'email_body') as $field) {
        if (isset($this[$field])) {
          unset($this[$field]);
        }
      }
    }
  }

  protected function doUpdateObject($values) {
    if (!$this instanceof WidgetStatusForm) {
      $stylings = array();
      foreach (array('type', 'width', 'title_color', 'body_color', 'button_color', 'bg_left_color', 'bg_right_color', 'form_title_color') as $i) {
        $stylings[$i] = $values['styling_' . $i];
        unset($values['styling_' . $i]);
      }
      $values['stylings'] = json_encode($stylings);
    }

    parent::doUpdateObject($values);
  }

}
