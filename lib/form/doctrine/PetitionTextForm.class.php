<?php

/**
 * PetitionText form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PetitionTextForm extends BasePetitionTextForm
{
  protected $state_count = true;

  public function getStateCount()
  {
    return $this->state_count;
  }

  static $defaults_petition = array
    (
    'title'                    => 'Insert a short and movtivating action title',
    'target'                   => 'Insert a subheading or the political target(s), e. g. "To the heads of state of the European Union"',
    'background'               => 'Insert a short text about the aim(s) of your campaign, maybe give some explanatory content. You may include links and media.',
    'intro'                    => 'The petition text will be split into 3 parts. This part (the intro) and the last part (the footer) should contain contextual information, e. g. references to the political addressee or to a specific event. Your partners and supporters will be able to modify this text for their own widgets. Put the relevant parts of your message into the 2. part of the petition (the body).',
    'body'                     => 'Put the relevant parts of your message into this part of the petition (the body). This text will remain the same throughout all widgets created for this campaign. Choose this text carefully. It should be as brief as possible.',
    'footer'                   => 'Insert a closing rate here, e. g. a reference to a specific event, your petition hand-over action or simply a complimentary close.',
    'email_validation_subject' => 'Confirm your action --- TITLE',
    'email_validation_body'    => "Hello,

you just signed the petition TITLE . To confirm your action, click here:

VALIDATION

We count your signature only if you click this link! After you confirmed your signature, please take a minute and forward the following email to your friends and family:

--

Hello, I just took part in this action: TITLE - TARGET.

Take action too: URL-REFERER

More information about the petition: BACKGROUND

The petition text: INTRO BODY FOOTER",
    'email_tellyour_subject' => 'Sign the petition --- TITLE ',
    'email_tellyour_body'    => "Hello,

I just took part in this action: TITLE - TARGET.

Take action too: URL-REFERER

More information about the petition: BACKGROUND

The petition text: INTRO BODY FOOTER"
  );

  static $defaults_email = array
    (
    'target'                   => 'Insert the name and maybe function of your political target(s), e. g. "José Manuel Barroso, president of the European Commission"',
    'email_subject'            => 'Insert the standard text for the subject-line of the email that will be sent to your political target.',
    'email_body'               => 'Insert the standard text for the email that will be sent to your political target.',
    'email_validation_subject' => 'Confirm your action --- TITLE',
    'email_validation_body'    => "Hello,

thank you for taking action. Before your email will be sent out to TARGET, you need to confirm your action. Click here:

VALIDATION

We will send your email to TARGET only if you click this link! After you confirmed your action, please take a minute and forward the following email to your friends and family:

--

Hello, I just took part in this email-action -- TITLE -- to TARGET.

Take action too: URL-REFERER

More information about the action: BACKGROUND

The email text: EMAIL-SUBJECT -- EMAIL-BODY",
    'email_tellyour_subject' => 'Sign the petition --- TITLE',
    'email_tellyour_body'    => "Hello,

I just took part in this email-action -- TITLE -- to TARGET.

Take action too: URL-REFERER

More information about the action: BACKGROUND

The email text: EMAIL-SUBJECT -- EMAIL-BODY"
  );

  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('policat');

    unset(
      $this['created_at'],
      $this['updated_at'],
      $this['petition_id'],
      $this['object_version'],
      $this['email_targets'],
      $this['widget_id']
    );

    $petition_text = $this->getObject();
    $petition = $petition_text->getPetition();

    $this->setWidget('title',         new sfWidgetFormInput(array(),    array('size' => 90)));
    $this->setWidget('target',        new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 3)));
    $this->setWidget('background',    new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5)));

    if (!$petition->isEmailKind())
    {
      $this->setWidget('intro',         new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5)));
      $this->setWidget('body',          new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 30)));
      $this->setWidget('footer',        new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5)));
      $this->getValidator('intro') ->setOption('required', false);
      $this->getValidator('body')  ->setOption('required', true);
      $this->getValidator('footer')->setOption('required', false);
      unset($this['email_subject'], $this['email_body']);
    }
    else
    {
      $this->setWidget('email_subject',  new sfWidgetFormInput(array(),    array('size' => 90)));
      $this->setWidget('email_body',     new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 30)));
      $this->getValidator('email_subject')->setOption('required', true);
      $this->getValidator('email_body')   ->setOption('required', true);
      unset($this['intro'], $this['body'], $this['footer']);
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
      } else {
        $keywords = PetitionSigningTable::$KEYWORDS;
      }
      $this->getWidgetSchema()->setHelp('email_body', 'You can use the following keywords: ' . implode(', ', $keywords) . '.');
    }

    $this->setWidget('email_validation_subject', new sfWidgetFormInput(array(),    array('size' => 90)));
    $this->setWidget('email_validation_body',    new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 8)));
    $this->setWidget('email_tellyour_subject',   new sfWidgetFormInput(array(),    array('size' => 90)));
    $this->setWidget('email_tellyour_body',      new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 8)));

    $possible_statuses = array_keys(PetitionText::$STATUS_SHOW);
    $this->state_count = count($possible_statuses);

    $possible_statuses_show = PetitionText::calcStatusShow($possible_statuses);

    $this->setWidget('status', new sfWidgetFormChoice(array('choices' => $possible_statuses_show)));
    $this->setValidator('status', new sfValidatorChoice(array('choices'  => $possible_statuses, 'required' => true)));

    if ($petition_text->getLanguageId() === null)
    {
      $this->setWidget('language_id', new sfWidgetFormDoctrineChoice(array(
          'model' => $this->getRelatedModelName('Language'),
          'add_empty' => false,
          'query' => Doctrine_Core::getTable('Language')
          ->createQuery('l')
          ->where('l.id NOT IN (SELECT pt.language_id FROM PetitionText pt WHERE pt.petition_id = ?)', $petition_text->getPetitionId())
      )));
      $this->setValidator('language_id', new sfValidatorDoctrineChoice(array(
          'model' => $this->getRelatedModelName('Language'),
          'query' => Doctrine_Core::getTable('Language')
          ->createQuery('l')
          ->where('l.id NOT IN (SELECT pt.language_id FROM PetitionText pt WHERE pt.petition_id = ?)', $petition_text->getPetitionId())
      )));
    }
    else
    {
      unset($this['language_id']);
    }

    $this->setWidget('privacy_policy_body', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 30)));

    if (!$petition_text->isNew())
    {
      $this->setWidget('updated_at', new sfWidgetFormInputHidden());
      $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $petition_text->getUpdatedAt())));

      $this->setWidget('widget_id', new sfWidgetFormDoctrineChoice(array(
          'model' => $this->getRelatedModelName('DefaultWidget'),
          'add_empty' => true,
          'method' => 'getIdentString',
          'query' => Doctrine_Core::getTable('Widget')
            ->createQuery('w')
            ->where('w.petition_text_id = ?', $petition_text->getId())
            ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
      )));

      $this->setValidator('widget_id', new sfValidatorDoctrineChoice(array(
          'model' => $this->getRelatedModelName('DefaultWidget'),
          'required' => false,
          'query' => Doctrine_Core::getTable('Widget')
            ->createQuery('w')
            ->where('w.petition_text_id = ?', $petition_text->getId())
            ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
      )));
    }

    // static defaults
    $defaults = self::$defaults_petition;
    if ($petition->isEmailKind())
      $defaults = array_merge($defaults, self::$defaults_email);
    foreach ($defaults as $def_key => $def_value) if (isset($this[$def_key])) $this->setDefault($def_key, $def_value);

    // copy defaults from existing text
    $copy = $this->getOption('copy');
    if (isset($copy))
      foreach (array('title' /*, 'teaser_header', 'teaser_button'*/, 'target', 'background', 'intro', 'body', 'footer',
        'email_validation_subject', 'email_validation_body', 'email_tellyour_subject', 'email_tellyour_body', 'email_subject', 'email_body') as $field)
        $this->setDefault($field, $copy[$field]);
  }
}
