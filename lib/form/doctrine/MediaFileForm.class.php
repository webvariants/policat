<?php

/**
 * MediaFile form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
class MediaFileForm extends BaseMediaFileForm {

  public function configure() {
    if ($this->getObject()->isNew()) {
      $this->useFields(array());
      $this->widgetSchema->setFormFormatterName('bootstrapinline');

      $this->setWidget('filename', new sfWidgetFormInputFile(array('label' => false)));
      $this->setValidator('filename', new sfValidatorFile(array(
          'required' => true,
          'mime_types' => 'web_images',
          'path' => $this->getObject()->getFilePath(),
          'max_size' => min($this->getObject()->getFreeSpace(), MediaFile::IMAGE_LIMIT)
        ), array(
          'max_size' => 'File is too large (maximum is %max_size% kilobytes).'
      )));
    } else {
      $this->useFields(array('title'));
      $this->widgetSchema->setFormFormatterName('bootstrap');
    }
  }

  protected function doUpdateObject($values) {
    $file = $this->getValue('filename');

    if ($file && $file instanceof sfValidatedFile) {
      $values['mimetype'] = $file->getType();
      $values['title'] = $file->getOriginalName();
      $values['extension'] = $file->getExtension();
      $values['size'] = $file->getSize();
    }

    parent::doUpdateObject($values);
  }

}
