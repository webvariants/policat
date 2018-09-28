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

  const NAME_LENGTH = 30;
  const EXT_LENGTH = 5;
  const FILENAME_PATTERN = '/^[a-z0-9_]{2,30}(\.[a-z0-9]{1,5})?$/';

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
      $and = new sfValidatorAnd(array(
          new sfValidatorRegex(array(
              'pattern' => self::FILENAME_PATTERN,
              'required' => true
            )),
          new ValidatorUniqueMediaFileTitle(array('object' => $this->getObject()))
      ));

      $this->setValidator('title', $and);
      $this->getWidgetSchema()->setHelp('title', 'Valid characters: _, a - z and 0 - 9, file extension allowed, lower case only');
      $this->widgetSchema->setFormFormatterName('bootstrap4');
    }
  }

  protected function doUpdateObject($values) {
    $file = $this->getValue('filename');

    if ($file && $file instanceof sfValidatedFile) {
      $values['mimetype'] = $file->getType();
      $values['title'] = $this->cleanTitle($file->getOriginalName(), $file->getExtension());
      $values['extension'] = $file->getExtension();
      $values['size'] = $file->getSize();
    }

    parent::doUpdateObject($values);
  }

  protected function cleanStr($str, $maxlen) {
    $a = preg_replace('/[^a-z0-9_]/', '_', mb_strtolower($str, 'UTF-8'));
    $b = preg_replace('/_{2,}/', '_', $a);
    $c = ltrim($b, '_');
    $d = substr($c, 0, $maxlen);
    $e = rtrim($d, '_');

    return $e;
  }

  protected function cleanTitle($title, $mimeext) {
    $ext = $this->cleanStr(pathinfo($title, PATHINFO_EXTENSION), self::EXT_LENGTH);
    $name = $this->cleanStr(pathinfo($title, PATHINFO_FILENAME), self::NAME_LENGTH);

    if (!$ext) {
      $ext = $mimeext;
    }

    if (!$name) {
      $name = 'file';
    }

    $petition = $this->getObject()->getPetition();
    if ($petition && !$petition->isNew()) {
      $unique = false;
      $i = 0;
      while (!$unique) {
        $name_i = $i === 0 ? $name : substr($name, 0, self::NAME_LENGTH - strlen($i) - 1) . '-' . $i;
        $unique = !MediaFileTable::getInstance()->createQuery('mf')
            ->where('mf.petition_id = ?', $petition->getId())
            ->andWhere('mf.title = ?', $name_i . '.' . $ext)
            ->count();
        $i++;
      }

      $name = $name_i;
    }

    return $name . '.' . $ext;
  }

}
