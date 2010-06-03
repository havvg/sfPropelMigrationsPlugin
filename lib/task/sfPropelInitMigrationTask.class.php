<?php

class sfPropelInitMigrationTask extends sfPropelBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'The name of the migration'),
    ));

    $this->aliases = array('init-migration');
    $this->namespace = 'propel';
    $this->name = 'init-migration';
    $this->briefDescription = 'Creates a new migration class file';
  }

  protected function execute($arguments = array(), $options = array())
  {
    $autoloader = sfSimpleAutoload::getInstance();
    $autoloader->addDirectory(sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfPropelMigrationsPlugin' . DIRECTORY_SEPARATOR . 'lib');

    $migrator = new sfPropelMigrator();

    if (!is_dir(sfPropelMigrationsPluginConfiguration::getMigrationsDir()))
    {
      $this->getFilesystem()->mkDirs(sfPropelMigrationsPluginConfiguration::getMigrationsDir());
    }

    try
    {
      $this->logSection('migrations', 'generating new migration stub');
      $filename = $migrator->initializeMigration($arguments['name']);
      $this->logSection('file+', $filename);
    }
    catch (RuntimeException $e)
    {
      $this->logSection('migrations', $e->getMessage(), null, 'ERROR');
    }
    catch (InvalidArgumentException $e)
    {
      $this->logSection('migrations', $e->getMessage(), null, 'ERROR');
    }
  }
}
