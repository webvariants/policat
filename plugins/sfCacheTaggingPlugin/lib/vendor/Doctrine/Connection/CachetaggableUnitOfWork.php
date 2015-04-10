<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * UnitOfWork to remove only tags through all relations with Cachetaggable
   * behavior. If relation is a Doctrine cascade delete, then it will skip, due
   * to automatical machanisms.
   *
   * Most part of code stolen from Doctrine_Connection_UnitOfWork witch
   * authors are:
   *   Konsta Vesterinen <kvesteri@cc.hut.fi>
   *   Roman Borschel <roman@code-factory.org>
   * Revision: 7684
   *
   * @package sfCacheTaggingPlugin
   * @subpackage doctrine
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class Doctrine_Connection_CachetaggableUnitOfWork extends Doctrine_Connection_Module
  {
    /**
     * Tag names for all nested relations to remove (CASCADE relation)
     *
     * @var array Assoc values Oid => tag name
     */
    protected $tagNamesToDelete = array();

    /**
     * Tag names for all nested relations to invalidate (SET NULL relation)
     *
     * @var array Assoc values Oid => tag name
     */
    protected $tagNamesToInvalidate = array();

    /**
     * @see Doctrine_Connection_UnitOfWork::delete() copy&past from
     *
     * @param Doctrine_Record $record
     * @return null
     */
    public function collectDeletionsAndInvalidations (Doctrine_Record $record)
    {
      $this->tagNamesToDelete = array();
      $this->tagNamesToInvalidate = array();

      # first record (root element) always goes to collection "tagNamesToDelete"
      $this->collect($record, $this->tagNamesToDelete);
    }

    /**
     * @return array As assoc array TagName => null
     */
    public function getDeletions ()
    {
      return array_flip(array_values($this->tagNamesToDelete));
    }

    /**
     * @return array As assoc array TagName => null
     */
    public function getInvalidations ()
    {
      return array_flip(array_values($this->tagNamesToInvalidate));
    }

    /**
     * @see Doctrine_Connection_UnitOfWork::_collectDeletions() copy&past from
     *
     * @param Doctrine_Record $record
     * @param array $definitions
     * @return null
     */
    private function collect (Doctrine_Record $record, & $definitions)
    {
      if (! $record->exists())
      {
        return;
      }

      if (! $record->getTable()->hasTemplate(sfCacheTaggingToolkit::TEMPLATE_NAME))
      {
        return;
      }

      # delete definitions
      if ($this->tagNamesToDelete === $definitions)
      {
        $definitions[$record->getOid()] = $record->obtainTagName();

        $this->cascade($record);
      }
      else # invalidate definitions
      {
        # do not call cascade - due to SET NULL only updates columns

        # do not add tag, if its already on deletion list
        if (! array_key_exists($record->getOid(), $this->tagNamesToDelete))
        {
          $definitions[$record->getOid()] = $record->obtainTagName();
        }
      }
    }

    /**
     * @see Doctrine_Connection_UnitOfWork::_cascadeDelete()
     *      (most part copy&past from)
     *
     * @param Doctrine_Record  The record for which the delete operation will be cascaded.
     * @throws PDOException    If something went wrong at database level
     * @return null
     */
    protected function cascade (Doctrine_Record $record)
    {
      foreach ($record->getTable()->getRelations() as $relation)
      {
        /* @var $relation Doctrine_Relation_LocalKey */

        # build-in Doctrine cascade mechanism do all the work - skip
        if ($relation->isCascadeDelete())
        {
          continue;
        }

        $cascade = $relation->offsetGet('cascade');

        # no instructions, no results - skip
        if (0 == count($cascade))
        {
          continue;
        }

        $isCascadeDeleteTags = in_array('deleteTags', $cascade);
        $isCascadeInvalidateTags = in_array('invalidateTags', $cascade);

        # could be only 1 selected, otherwise skip
        if (! ($isCascadeDeleteTags xor $isCascadeInvalidateTags))
        {
          continue;
        }

        if ($isCascadeDeleteTags)
        {
          $definitions = & $this->tagNamesToDelete;
        }
        else
        {
          $definitions = & $this->tagNamesToInvalidate;
        }

        $fieldName = $relation->getAlias();

        if (
            $relation->getType() != Doctrine_Relation::ONE
          ||
            isset($record->$fieldName)
        )
        {
          $record->refreshRelated($relation->getAlias());
        }

        $relatedObjects = $record->get($relation->getAlias());

        if (
            $relatedObjects instanceof Doctrine_Record
          &&
            $relatedObjects->exists()
          &&
            ! isset($definitions[$relatedObjects->getOid()])
        )
        {
          # invalidate collection version too
          $collectionName = sfCacheTaggingToolkit::obtainCollectionName(
            $relatedObjects->getTable()
          );

          if ($isCascadeDeleteTags)
          {
            $this->tagNamesToInvalidate[$collectionName] = $collectionName;
          }
          elseif ($isCascadeInvalidateTags)
          {
            $template = $relatedObjects
              ->getTable()
              ->getTemplate(sfCacheTaggingToolkit::TEMPLATE_NAME);

            if ($template->getOption('invalidateCollectionVersionOnUpdate'))
            {
              $this->tagNamesToInvalidate[$collectionName] = $collectionName;
            }
          }

          $this->collect($relatedObjects, $definitions);

          continue;
        }

        if (
            ! $relatedObjects instanceof Doctrine_Collection
          ||
            count($relatedObjects) == 0
          ||
            ! $relatedObjects->getTable()->hasTemplate(sfCacheTaggingToolkit::TEMPLATE_NAME)
        )
        {
          continue;
        }

        # invalidate collection version too
        $collectionName = sfCacheTaggingToolkit::obtainCollectionName(
          $relatedObjects->getTable()
        );

        if ($isCascadeDeleteTags)
        {
          $this->tagNamesToInvalidate[$collectionName] = $collectionName;
        }
        elseif ($isCascadeInvalidateTags)
        {
          $template = $relatedObjects
            ->getTable()
            ->getTemplate(sfCacheTaggingToolkit::TEMPLATE_NAME);

          if ($template->getOption('invalidateCollectionVersionOnUpdate'))
          {
            $this->tagNamesToInvalidate[$collectionName] = $collectionName;
          }
        }

        foreach ($relatedObjects as $object)
        {
          if (isset($definitions[$object->getOid()]))
          {
            continue;
          }

          $this->collect($object, $definitions);
        }
      }

    }
  }

