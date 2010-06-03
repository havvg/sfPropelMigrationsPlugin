<?php

class sfPropelMigrator
{
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
}