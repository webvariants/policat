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
        $this->useFields(array());

        $this->validatorSchema->setOption('allow_extra_fields', true);
        $this->setWidget('file', new sfWidgetFormInputFile());                
        $this->widgetSchema->setNameFormat('mediaFile[%s]');

        $this->setValidator('file', new sfValidatorFile());
    }

}
