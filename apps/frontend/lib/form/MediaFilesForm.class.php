<?php

/**
 * MediaFiles form.
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
class MediaFilesForm extends BaseMediaFilesForm {

    public function configure() {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'petition_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
            'filename' => new sfWidgetFormInputText(),
            'name' => new sfWidgetFormInputText(),
            'mimetype' => new sfWidgetFormInputText(),
            'title' => new sfWidgetFormInputText(),
            'path' => new sfWidgetFormInputText(),
            'extention' => new sfWidgetFormInputText(),
            'size' => new sfWidgetFormInputText(),
            'sort_order' => new sfWidgetFormInputText(),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'petition_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id','required' => false)),
            'filename' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'name' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'mimetype' => new sfValidatorString(array('max_length' => 40, 'required' => false)),
            'title' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'path' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'extention' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
            'size' => new sfValidatorInteger(array('required' => false)),
            'sort_order' => new sfValidatorInteger(array('required' => false)),
            'created_at' => new sfValidatorDateTime(array('required' => false)),
            'updated_at' => new sfValidatorDateTime(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('media_files[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

}
