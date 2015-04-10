<?php
class WidgetFormDoctrineSelectDoubleList extends sfWidgetFormSelectDoubleList
{
  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = array();
    parent::__construct($options, $attributes);
    $this->setOption('choices', $this->getChoices());
  }

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * model:        The model class (required)
   *  * method:       The method to use to display object values (__toString by default)
   *  * key_method:   The method to use to display the object keys (getPrimaryKey by default)
   *  * order_by:     An array composed of two fields:
   *                    * The column to order by the results (must be in the PhpName format)
   *                    * asc or desc
   *  * query:        A query to use when retrieving objects
   *  * table_method: A method to return either a query, collection or single object
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('order_by', null);
    $this->addOption('query', null);
    $this->addOption('table_method', null);

    parent::configure($options, $attributes);
  }

  /**
   * Returns the choices associated to the model.
   *
   * @return array An array of choices
   */
  public function getChoices()
  {
    $choices = array();    

    if (null === $this->getOption('table_method'))
    {
      $query = null === $this->getOption('query') ? Doctrine_Core::getTable($this->getOption('model'))->createQuery() : $this->getOption('query');
      if ($order = $this->getOption('order_by'))
      {
        $query->addOrderBy($order[0] . ' ' . $order[1]);
      }
      $objects = $query->execute();
    }
    else
    {
      $tableMethod = $this->getOption('table_method');
      $results = Doctrine_Core::getTable($this->getOption('model'))->$tableMethod();

      if ($results instanceof Doctrine_Query)
      {
        $objects = $results->execute();
      }
      else if ($results instanceof Doctrine_Collection)
      {
        $objects = $results;
      }
      else if ($results instanceof Doctrine_Record)
      {
        $objects = new Doctrine_Collection($this->getOption('model'));
        $objects[] = $results;
      }
      else
      {
        $objects = array();
      }
    }

    $method = $this->getOption('method');
    $keyMethod = $this->getOption('key_method');

    foreach ($objects as $object)
    {
      $choices[$object->$keyMethod()] = $object->$method();
    }

    return $choices;
  }
}