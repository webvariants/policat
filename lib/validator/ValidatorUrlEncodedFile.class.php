<?php

class ValidatorUrlEncodedFile extends sfValidatorFile
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max_size:             The maximum file size in bytes (cannot exceed upload_max_filesize in php.ini)
   *  * mime_types:           Allowed mime types array or category (available categories: web_images)
   *  * mime_type_guessers:   An array of mime type guesser PHP callables (must return the mime type or null)
   *  * mime_categories:      An array of mime type categories (web_images is defined by default)
   *  * path:                 The path where to save the file - as used by the sfValidatedFile class (required)
   *  * validated_file_class: Name of the class that manages the cleaned uploaded file (optional)
   *
   * There are 3 built-in mime type guessers:
   *
   *  * guessFromFileinfo:        Uses the finfo_open() function (from the Fileinfo PECL extension)
   *  * guessFromMimeContentType: Uses the mime_content_type() function (deprecated)
   *  * guessFromFileBinary:      Uses the file binary (only works on *nix system)
   *
   * Available error codes:
   *
   *  * max_size
   *  * mime_types
   *  * cant_write
   *  * extension
   *  * url_parsing
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addOption('validated_file_class', 'ValidatedUrlEncodedFile');
    $this->addRequiredOption('path');
    $this->addMessage('url_parsing', 'Error on URL parsing.');
  }

  /**
   * This validator always returns a ValidatedUrlEncodedFile object.
   *
   * The input value must be an array with the following keys:
   *
   *  * tmp_name: The absolute temporary path to the file
   *  * name:     The original file name (optional)
   *  * type:     The file content type (optional)
   *  * error:    The error code (optional)
   *  * size:     The file size in bytes (optional)
   *
   * @see sfValidatorBase
   */
  protected function doClean($dataUrl)
  {
    if (strpos($dataUrl, 'data:') !== 0) {
      throw new sfValidatorError($this, 'url_parsing');
    }
    $data_start = strpos($dataUrl, ',');
    if (!$data_start || $data_start < 6 || strlen($dataUrl) < $data_start + 2) {
      throw new sfValidatorError($this, 'url_parsing');
    }
    $meta = substr($dataUrl, 5, $data_start - 5);
    $data = substr($dataUrl, $data_start + 1);
    $metas = explode(';', $meta);
    if (count($metas) !== 3) {
      throw new sfValidatorError($this, 'url_parsing');
    }
    $mimeType = $metas[0];
    if ($metas[2] !== 'base64') {
      throw new sfValidatorError($this, 'url_parsing');
    }

    $name = substr($metas[1], 5);

    $data = base64_decode($data, true);
    if ($data === false) {
      throw new sfValidatorError($this, 'url_parsing');
    }

    $size = strlen($data);

    // check file size
    if ($this->hasOption('max_size') && $this->getOption('max_size') < (int) $size)
    {
      throw new sfValidatorError($this, 'max_size', array(
        'max_size' => round($this->getOption('max_size') / 1024, 0),
        'size' => (int) $size
      ));
    }

    // check mime type
    if ($this->hasOption('mime_types'))
    {
      $mimeTypes = is_array($this->getOption('mime_types')) ? $this->getOption('mime_types') : $this->getMimeTypesFromCategory($this->getOption('mime_types'));
      if (!in_array($mimeType, array_map('strtolower', $mimeTypes)))
      {
        throw new sfValidatorError($this, 'mime_types', array('mime_types' => $mimeTypes, 'mime_type' => $mimeType));
      }
    }

    $class = $this->getOption('validated_file_class');

    return new $class($name, $mimeType, $data, $size, $this->getOption('path'));
  }

  /**
   * @see sfValidatorBase
   */
  protected function isEmpty($value)
  {
    return !is_string($value) || !$value;
  }

}
