<?php

class sfPropelMigrator
{
  const EXCEPTION_MIGRATION_DIR_NOT_WRITABLE = 'The directory for the new migration file is not writable.';

  const EXCEPTION_MIGRATION_NOT_WRITTEN = 'The content could not be written to the new migration file.';

  /**
   * The list of initialized migrations.
   *
   * @var sfPropelMigrations
   */
  protected $migrations = array();

  public function __construct()
  {
    $this->migrations = $this->loadMigrations();
  }

  /**
   * Find all initialized migrations.
   *
   * @return sfPropelMigrations
   */
  protected function loadMigrations()
  {
    $files = sfFinder::type('file')->name('/^\d{14}.*\.php$/')->maxdepth(1)->in(sfPropelMigrationsPluginConfiguration::getMigrationsDir());

    if (empty($files))
    {
      return array();
    }
    else
    {
      $migrations = array();

      foreach ($files as $key => $filename)
      {
        $migration = sfPropelMigration::createInstanceFromFilename($filename);
        $migrations[] = $migration;
      }

      return new sfPropelMigrations($migrations);
    }
  }

  /**
   * Returns the list of initialized migrations.
   *
   * @return sfPropelMigrations
   */
  public function getMigrations()
  {
    return $this->migrations;
  }

  /**
   * Intializes a new migration.
   *
   * @throws RuntimeException
   *
   * @param string $name The human readable name for this migration.
   *
   * @return string The filename for the new migration.
   */
  public function initializeMigration($name)
  {
    $version = (microtime(true) * 10000);

    $migration = new sfPropelMigration($version, $name);
    $skeleton = sfPropelMigrationSkeleton::getSkeleton($version);

    if (!is_dir($migration->getDirname()))
    {
      $fs = new sfFilesystem();
      $fs->mkdirs($migration->getDirname());
    }

    if (is_writable($migration->getDirname()))
    {
      if (file_put_contents($migration->getFullFilename(), $skeleton) === false)
      {
        throw new RuntimeException(self::EXCEPTION_MIGRATION_NOT_WRITTEN);
      }
    }
    else
    {
      throw new RuntimeException(self::EXCEPTION_MIGRATION_DIR_NOT_WRITABLE);
    }

    return $migration->getFilename();
  }
}