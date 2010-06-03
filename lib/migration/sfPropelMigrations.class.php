<?php

class sfPropelMigrations implements Iterator, Countable
{
  const EXCEPTION_INVALID_LIST = 'The given list of migrations is invalid.';

  /**
   * The list of migrations.
   *
   * @var array
   */
  protected $migrations = array();

  /**
   * Constructor.
   *
   * @throws InvalidArgumentException
   *
   * @param array $migrations Array of sfPropelMigration.
   *
   * @return void
   */
  public function __construct(array $migrations = array())
  {
    if (!empty($migrations))
    {
      /* @var $eachMigration sfPropelMigration */
      foreach ($migrations as $eachMigration)
      {
        if ($eachMigration instanceof sfPropelMigration)
        {
          $this->addMigration($eachMigration);
        }
        else
        {
          throw new InvalidArgumentException(self::EXCEPTION_INVALID_LIST);
        }
      }
    }
  }

  /**
   * Add a new migration to this list.
   *
   * @param sfPropelMigration $migration
   *
   * @return sfPropelMigrations (this)
   */
  public function addMigration(sfPropelMigration $migration)
  {
    if (empty($this->migrations[$migration->getVersion()]))
    {
      $this->migrations[$migration->getVersion()] = $migration;
    }

    ksort($this->migrations);
    $this->rewind();

    return $this;
  }

  // Countable Interface
  public function count()
  {
    return count($this->migrations);
  }

  // Iterator Interface
  protected $position = 0;

  /**
   * Returns the current object for the Iterator.
   *
   * @return sfPropelMigration
   */
  public function current()
  {
    $keys = array_keys($this->migrations);

    return $this->migrations[$keys[$this->position]];
  }

  public function rewind()
  {
    $this->position = 0;
  }

  public function valid()
  {
    return $this->position < sizeof($this->migrations);
  }

  public function key()
  {
    return $this->position;
  }

  public function next()
  {
    $this->position++;
  }
}