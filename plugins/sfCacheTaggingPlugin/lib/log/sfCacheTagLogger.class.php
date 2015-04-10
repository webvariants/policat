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
   * Due the priority logic in sfLogger, was created sfCacheLogger
   *
   * @package sfCacheTaggingPlugin
   * @subpackage log
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  abstract class sfCacheTagLogger
  {
    /**
     * Default logger format
     *
     * Available arguments combinations: %char%, %char_explanation%, %time%, %key%, %EOL%
     *
     * @var string
     */
    protected $format = '%char%';

    /**
     * Date format
     *
     * @link http://php.net/manual/en/function.strftime.php
     * @var string
     */
    protected $timeFormat = '%Y-%b-%d %T%z';

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Class constructor.
     *
     * @see initialize()
     */
    public function __construct (array $options = array())
    {
      $this->initialize($options);

      if ($this->getOption('auto_shutdown'))
      {
        register_shutdown_function(array($this, 'shutdown'));
      }
    }

    /**
     * Initializes this sfCacheTagLogger instance.
     *
     * @param array $options An array of options.
     * @return null
     */
    public function initialize (array $options = array())
    {
      $this->options = array_merge(
        array(
          'auto_shutdown' => true,
          'skip_chars' => '',
        ),
        $options
      );

      if (null !== ($timeFormat = $this->getOption('time_format')))
      {
        $this->timeFormat = $timeFormat;
      }

      if (null !== ($format = $this->getOption('format')))
      {
        $this->format = $format;
      }
    }

    /**
     * Returns the options for the logger instance.
     */
    public function getOptions()
    {
      return $this->options;
    }

    /**
     * Sets option value
     */
    public function setOption ($name, $value)
    {
      $this->options[$name] = $value;
    }

    /**
     * Returns the value by its name
     *
     * @param string $name Option name
     * @param mixed  $default Return this value if option does not exists
     * @return mixed|null
     */
    public function getOption ($name, $default = null)
    {
      return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @param string $char  One character
     * @param string $key   Cache name or tag name with version
     *                      (e.g. "CompanyArticle_1(947568127349582")
     */
    abstract protected function doLog ($char, $key);

    /**
     * Logs a message.
     *
     * @param string $char  One character
     * @param string $key   Cache name or tag name
     *                      (e.g. "CompanyArticle_1" or "top-10-en-posts")
     */
    public function log ($char, $key)
    {
      if (false !== strpos($this->getOption('skip_chars'), $char))
      {
        return false;
      }

      return (boolean) $this->doLog($char, $key);
    }

    /**
     * Retuns char explanation
     *
     * @param string $char
     * @return string
     */
    protected function explainChar ($char)
    {
      switch ($char)
      {
        # Data:
        case 'g': return 'data cache not found or expired';
        case 'G': return 'data cache was found';
        case 'h': return 'cache dot not have data accessed by key';
        case 'H': return 'cache have data accessed by key';
        case 'l': return 'could not lock the data cache';
        case 'L': return 'data cache was locked for writing';
        case 's': return 'could not write new values to the cache';
        case 'S': return 'new values are saved to the data cache';
        case 'u': return 'could not unlock the cache';
        case 'U': return 'cache was unlocked';
        case 'r': return 'data cache with no locks';
        case 'R': return 'data cache with lock';

        # Tags:
        case 'v': return 'cache tag version is expired';
        case 'V': return 'cache tag version is up-to-date';
        case 'p': return 'could not write new version of tag';
        case 'P': return 'tag was updated with new a version';
        case 'e': return 'could not remove tag version';
        case 'E': return 'tag was removed';
        case 't': return 'tag does not exists';
        case 'T': return 'tag was found';
        case 'i': return 'cache does not have tag accessed by key';
        case 'I': return 'cache have tag accessed by key';

        default:
          return 'Unregistered char';
          break;
      }
    }

    /**
     * Executes the shutdown method.
     */
    public function shutdown ()
    {

    }
  }