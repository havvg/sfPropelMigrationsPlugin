<?php

abstract class sfBaseMigration
{
  const EXCEPTION_INVALID_QUERY_STRING = 'The given query string is invalid.';

  const EXCEPTION_INVALID_MIGRATION_DIRECTION = 'The given migration direction does not exist.';

  const EXCEPTION_INVALID_LIMITED_DATABASES_LIST = 'The given list of databases to limit the migration to is invalid.';

  const MIGRATION_UP = 1;

  const MIGRATION_DOWN = 2;

  /**
   * The current database connection.
   *
   * @var PropelPDO
   */
  protected $connection = null;

  /**
   * A list of all opened connections.
   *
   * @var array of PropelPDO
   */
  protected $openConnections = array();

  /**
   * A list of database names limiting where to migrate.
   *
   * @var array
   */
  protected $limitedDatabases = array();

  /**
   * Set the list of databases to migrate only. Defaults to all databases.
   *
   * @param array $databases
   *
   * @return sfMigration (this)
   */
  public function setLimitedDatabases(array $databases)
  {
    foreach ($databases as $eachDatabaseName)
    {
      if (!is_string($eachDatabaseName) || empty($eachDatabaseName))
      {
        throw new sfPropelMigrationException(self::EXCEPTION_INVALID_LIMITED_DATABASES_LIST);
      }
    }

    $this->limitedDatabases = $databases;

    return $this;
  }

  /**
   * Execute this migration.
   *
   * @throws sfPropelMigrationException
   *
   * @param int $direction sfBaseMigration::MIGRATION_UP or ::MIGRATION_DOWN. Defaults to ::MIGRATION_UP.
   *
   * @return bool Whether the migrations were successful.
   */
  public function migrate($direction = self::MIGRATION_UP)
  {
    $direction = intval($direction);
    if ($direction !== self::MIGRATION_UP and $direction !== self::MIGRATION_DOWN)
    {
      throw new sfPropelMigrationException(self::EXCEPTION_INVALID_MIGRATION_DIRECTION);
    }

    switch ($direction)
    {
      case self::MIGRATION_UP:
        $this->preUp();

        $this->useDatabase();
        $this->up();

        $success = $this->closeConnections();

        $this->postUp($success);
        break;

      case self::MIGRATION_DOWN:
        $failed = !empty($this->openConnections);

        $this->preDown();

        $this->useDatabase();
        $this->down($failed);

        $success = $this->closeConnections();

        $this->postDown($success);
        break;
    }

    return $success;
  }

  /**
   * The migration process itself.
   *
   * @return bool
   */
  abstract protected function up();

  /**
   * The process to revert this migration.
   *
   * @param bool $failed A flag whether the down is called because of a failed up. Defaults to false (normal down action).
   *                     The down process will only be run on the databases that have been migrated upwards before.
   *
   * @return bool
   */
  abstract protected function down($failed = false);

  /**
   * A method that is executed before the up method is called.
   *
   * @return sfMigration (this)
   */
  protected function preUp()
  {
    return $this;
  }

  /**
   * A method that is executed after the up method has finished.
   *
   * @param bool $success A flag whether the migrations were successful.
   *
   * @return sfMigration (this)
   */
  protected function postUp($success)
  {
    return $this;
  }

  /**
   * A method that is executed before the down method is called.
   *
   * @return sfMigration (this)
   */
  protected function preDown()
  {
    return $this;
  }

  /**
   * A method that is executed after the down method has finished.
   *
   * @param bool $success A flag whether the migrations were successful.
   *
   * @return sfMigration (this)
   */
  protected function postDown($success)
  {
    return $this;
  }

  /**
   * Returns the default database name for all migrations.
   * Defaults to the Propel default database.
   *
   * @return string
   */
  protected function getDefaultDBName()
  {
    return Propel::getDefaultDB();
  }

  /**
   * Switch the used database.
   *
   * @param string $dbName The name of the database registered to the Propel ORM.
   *
   * @return bool Flag whether the database has been switched.
   */
  protected function useDatabase($dbName = null)
  {
    if (is_null($dbName))
    {
      $dbName = $this->getDefaultDBName();
    }

    if (empty($this->limitedDatabases) or in_array($dbName, $this->limitedDatabases))
    {
      $con = Propel::getConnection($dbName);

      if (empty($this->openConnections[$dbName]))
      {
        $con->beginTransaction();
        $this->openConnections[$dbName] = $con;
      }

      $this->setConnection($con);

      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Closes and commits (or rolls back) all open connections.
   *
   * @return bool
   */
  protected function closeConnections()
  {
    $commit = true;
    $committedConnections = array();

    /* @var $eachConnection PropelPDO */
    foreach ($this->openConnections as $dbName => $eachConnection)
    {
      // try to commit, if fails, roll back all other connections
      if (!$commit or !$eachConnection->commit())
      {
        $commit = false;
        $eachConnection->rollBack();
      }
      else
      {
        // save which connections have been committed before starting to rollback
        $committedConnections[] = $dbName;
      }
    }

    if (!$commit and !empty($committedConnections))
    {
      $this->setLimitedDatabases($committedConnections);

      // so we need to run the down part
      $this->migrate(self::MIGRATION_DOWN);
    }

    return $commit;
  }

  /**
   * Sets the current database connection to the given one.
   *
   * @param PropelPDO $con
   *
   * @return sfMigration (this)
   */
  protected function setConnection(PropelPDO $con)
  {
    $this->connection = $con;

    return $this;
  }

  /**
   * Returns the current database connection.
   *
   * @return PropelPDO
   */
  protected function getConnection()
  {
    return $this->connection;
  }

  /**
   * Execute an SQL query. A shortcut method on the current database connection.
   *
   * @uses PropelPDO::prepare()
   * @uses PDOStatement::execute()
   *
   * @param string $sql
   * @param array $driverOptions Options passed to the PDO driver.
   *
   * @return mixed Result of the query.
   */
  protected function executeQuery($sql, array $driverOptions = array())
  {
    if (empty($sql) || !is_string($sql))
    {
      throw new sfPropelMigrationException(self::EXCEPTION_INVALID_QUERY_STRING);
    }

    return $this->getConnection()->prepare($sql, $driverOptions)->execute();
  }
}