<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * @package sfCacheTaggingPlugin
   * @subpackage doctrine
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  abstract class sfCachetaggableDoctrineRecord extends sfDoctrineRecord
  {
    /**
     * @var string $templateName Template (behavior) name
     * @return Doctrine_Template
     */
    protected function getTempleteWithInvoker ($templateName, $method)
    {
      $table = $this->getTable();

      if ( ! $table->hasTemplate($templateName))
      {
        throw new InvalidArgumentException(
          sprintf('Template "%s" is not registered', $templateName)
        );
      }

      $template = $table->getTemplate($templateName);
      $template->setInvoker($this);

      $table->setMethodOwner($method, $template);

      return $template;
    }

    /**
     * @return Doctrine_Template_Cachetaggable
     */
    protected function getCachetaggable ($method)
    {
      return $this->getTempleteWithInvoker(
        sfCacheTaggingToolkit::TEMPLATE_NAME, $method
      );
    }

    /**
     * @return Doctrine_Record
     */
    public function updateObjectVersion ()
    {
      return $this->getCachetaggable('updateObjectVersion')->updateObjectVersion();
    }

    /**
     * @see Doctrine_Template_Cachetaggable::getCacheTags()
     * @var boolean $deep (whether to fetch tags recursively from all joined tables)
     * @return array Object tags
     */
    public function getCacheTags ($deep = true)
    {
      return $this
        ->getCachetaggable('getCacheTags')
        ->getCacheTags($deep)
      ;
    }

    /**
     * @see Doctrine_Template_Cachetaggable::obtainTagName()
     * @return string
     */
    public function obtainTagName ()
    {
      return $this->getCachetaggable('obtainTagName')->obtainTagName();
    }

    /**
     * @see Doctrine_Template_Cachetaggable::obtainObjectVersion()
     * @return string
     */
    public function obtainObjectVersion ()
    {
      return $this->getCachetaggable('obtainObjectVersion')->obtainObjectVersion();
    }

    /**
     * @see Doctrine_Template_Cachetaggable::getCollectionTags()
     * @return array
     */
    public function getCollectionTags ()
    {
      return $this->getCachetaggable('getCollectionTags')->getCollectionTags();
    }

    /**
     * @see Doctrine_Template_Cachetaggable::obtainCollectionName()
     * @return string
     */
    public function obtainCollectionName ()
    {
      return $this->getCachetaggable('obtainCollectionName')->obtainCollectionName();
    }

    /**
     * @see Doctrine_Template_Cachetaggable::obtainCollectionVersion()
     * @return string
     */
    public function obtainCollectionVersion ()
    {
      return $this->getCachetaggable('obtainCollectionVersion')->obtainCollectionVersion();
    }

    /**
     * Generates tags for refTable
     *
     * @param string    $alias
     * @param array     $ids
     * @return boolean
     */
    protected function getTagNamesByAlias ($alias, $ids)
    {
      if (0 == count($ids))
      {
        return;
      }

      $relation = $this->getTable()->getRelation($alias);

      if (! $relation instanceof Doctrine_Relation_Association)
      {
        return;
      }

      /* @var $refTable Doctrine_Table */
      $refTable = $relation->getAssociationTable();

      if (! $refTable->hasTemplate(sfCacheTaggingToolkit::TEMPLATE_NAME))
      {
        return;
      }

      $template = $refTable->getTemplate(sfCacheTaggingToolkit::TEMPLATE_NAME);

      $values = array();

      foreach ($ids as $id)
      {
        foreach ($refTable->getIdentifierColumnNames() as $columnName)
        {
          if ($relation->getLocal() == $columnName)
          {
            $values[$id][$columnName] = $this->getPrimaryKey();
          }
          else
          {
            $values[$id][$columnName] = $id;
          }
        }
      }

      $uniqueColumns = $template->getOptionUniqueColumns();
      $keyFormat = $template->getOptionKeyFormat($uniqueColumns);

      $tagNames = array();

      foreach ($values as $columnValues)
      {
        $tagName = sfCacheTaggingToolkit::buildTagKey(
          $template, $keyFormat, array_values($columnValues)
        );

        $tagNames[$tagName] = true;
      }

      return $tagNames;
    }

    /**
     * @see Doctrine_Record::link()
     * @return sfCachetaggableDoctrineRecord
     */
    public function link ($alias, $ids, $now = false)
    {
      $self = parent::link($alias, $ids, $now);

      try
      {
        $taggingCache = sfCacheTaggingToolkit::getTaggingCache();
      }
      catch (sfException $e)
      {
        return $self;
      }

      $tagNames = $this->getTagNamesByAlias($alias, $ids);

      if (is_array($tagNames))
      {
        $taggingCache->invalidateTags($tagNames);
      }

      return $self;
    }

    /**
     * @see Doctrine_Record::unlink()
     * @return sfCachetaggableDoctrineRecord
     */
    public function unlink ($alias, $ids = array(), $now = false)
    {
      $self = parent::unlink($alias, $ids, $now);

      try
      {
        $taggingCache = sfCacheTaggingToolkit::getTaggingCache();
      }
      catch (sfException $e)
      {
        return $self;
      }

      $tagNames = $this->getTagNamesByAlias($alias, $ids);

      if (is_array($tagNames))
      {
        $taggingCache->deleteTags($tagNames);
      }

      return $self;
    }
  }