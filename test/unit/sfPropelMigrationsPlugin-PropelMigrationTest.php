<?php
require_once(dirname(__FILE__) . '/../bootstrap/unit.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfPropelMigrationsPlugin/data/fixtures');

$limeTest = new lime_test(17, new lime_output_color());

try
{
  $propelMigration = new sfPropelMigration(0, 'valid-name');
  $limeTest->fail('InvalidArgumentException was not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught.');
  $limeTest->is($e->getMessage(), sfPropelMigration::EXCEPTION_INVALID_VERSION, 'Correct Exception.');
}

try
{
  $propelMigration = new sfPropelMigration(1, '');
  $limeTest->fail('InvalidArgumentException was not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught.');
  $limeTest->is($e->getMessage(), sfPropelMigration::EXCEPTION_INVALID_NAME, 'Correct Exception.');
}

try
{
  $propelMigration = @sfPropelMigration::createInstanceFromFilename();
  $limeTest->fail('InvalidArgumentException was not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught.');
  $limeTest->is($e->getMessage(), sfPropelMigration::EXCEPTION_INVALID_FILENAME, 'Correct Exception.');
}

try
{
  $propelMigration = sfPropelMigration::createInstanceFromFilename('invalid-file-name');
  $limeTest->fail('InvalidArgumentException was not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught.');
  $limeTest->is($e->getMessage(), sfPropelMigration::EXCEPTION_INVALID_FILENAME, 'Correct Exception.');
}

try
{
  $propelMigration = sfPropelMigration::createInstanceFromFilename(5);
  $limeTest->fail('InvalidArgumentException was not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught.');
  $limeTest->is($e->getMessage(), sfPropelMigration::EXCEPTION_INVALID_FILENAME, 'Correct Exception.');
}

$propelMigration = new sfPropelMigration(1, 'valid-name');
$limeTest->isa_ok($propelMigration, 'sfPropelMigration', 'Created sfPropelMigration.');

$fixturesMigrationsDir = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfPropelMigrationsPlugin' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'migrations';
sfConfig::set('sf_propelmigrationsplugin_migrationsdir', $fixturesMigrationsDir);
$limeTest->is(sfPropelMigrationsPluginConfiguration::getMigrationsDir(), $fixturesMigrationsDir, 'Configured MigrationsDirectory.');

try
{
  $propelMigration = sfPropelMigration::createInstanceFromFilename($fixturesMigrationsDir . DIRECTORY_SEPARATOR . 'first_test_migration' . DIRECTORY_SEPARATOR . '12755561356512_first_test_migration.php');
  $limeTest->fail('InvalidArgumentException was not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught.');
  $limeTest->is($e->getMessage(), sfPropelMigration::EXCEPTION_INVALID_FILENAME, 'Correct Exception.');
}

$propelMigration = sfPropelMigration::createInstanceFromFilename($fixturesMigrationsDir . DIRECTORY_SEPARATOR . 'first_test_migration' . DIRECTORY_SEPARATOR . '12755561356551_first_test_migration.php');
$limeTest->isa_ok($propelMigration, 'sfPropelMigration', 'Created sfPropelMigration.');
$limeTest->is($propelMigration->getVersion(), 12755561356551, 'Correct Version.');
$limeTest->is($propelMigration->getName(), 'first_test_migration', 'Correct Name.');