<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Cache key and tag logger
   *
   * @package sfCacheTaggingPlugin
   * @subpackage log
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfFileCacheTagLogger extends sfCacheTagLogger
  {
    /**
     * File pointer on log file
     *
     * @var resource
     */
    protected $fp = null;

    /**
     * @see sfCacheLogger::initialize()
     * @param array $options
     */
    public function initialize (array $options = array())
    {
      parent::initialize(
        array_merge(
          array(
            'file_mode' => 0640,
            'dir_mode' => 0750,
          ),
          $options
        )
      );

      if (null === ($file = $this->getOption('file')))
      {
        throw new sfConfigurationException(
          'You must provide a "file" parameter for this logger.'
        );
      }

      $dir = dirname($file);

      $umask = umask();
      umask(0000);
      if (! is_dir($dir))
      {
        mkdir($dir, $this->getOption('dir_mode'), true);
      }

      $fileExists = file_exists($file);

      if (! is_writable($dir) || ($fileExists && ! is_writable($file)))
      {
        throw new sfFileException(sprintf(
          'Unable to open the log file "%s" for writing.', $file
        ));
      }

      $this->fp = fopen($file, 'a');

      if (! $fileExists)
      {
        chmod($file, $this->getOption('file_mode'));
      }

      umask($umask);
    }

    protected function doLog ($char, $key)
    {
      if (flock($this->fp, LOCK_EX))
      {
        fwrite($this->fp, strtr($this->format, array(
          '%char%'              => $char,
          '%char_explanation%'  => $this->explainChar($char),
          '%key%'               => $key,
          '%time%'              => strftime($this->timeFormat),
          '%microtime%'         => sprintf("%0.0f", pow(10, 5) * microtime(true)),
          '%EOL%'               => PHP_EOL,
        )));

        flock($this->fp, LOCK_UN);
      }

      return true;
    }

    /**
     * Executes the shutdown method.
     */
    public function shutdown ()
    {
      if (is_resource($this->fp))
      {
        return fclose($this->fp);
      }

      return false;
    }
  }
