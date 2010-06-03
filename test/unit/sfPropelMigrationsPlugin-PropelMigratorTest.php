<?php
require_once(dirname(__FILE__) . '/../bootstrap/unit.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfPropelMigrationsPlugin/data/fixtures');

$limeTest = new lime_test(12, new lime_output_color());

$fixturesMigrationsDir = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfPropelMigrationsPlugin' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'migrations';
sfConfig::set('sf_propelmigrationsplugin_migrationsdir', $fixturesMigrationsDir);
$limeTest->is(sfPropelMigrationsPluginConfiguration::getMigrationsDir(), $fixturesMigrationsDir, 'Configured MigrationsDirectory.');

$propelMigrator = new sfPropelMigrator();
$migrations = $propelMigrator->getMigrations();
$limeTest->isa_ok($migrations, 'sfPropelMigrations', 'Got sfPropelMigrations.');
$limeTest->is(count($migrations), 3, 'Three test migrations found.');

// using Iterator to test content of list
$migrations->rewind();
$migration = $migrations->current();
$limeTest->isa_ok($migration, 'sfPropelMigration', 'First Valid Migration.');
$limeTest->is($migration->getVersion(), 12755561356551, 'Correct Version.');
$limeTest->is($migration->getName(), 'first_test_migration', 'Correct Name.');

$migrations->next();
$migration = $migrations->current();
$limeTest->isa_ok($migration, 'sfPropelMigration', 'Third Valid Migration.');
$limeTest->is($migration->getVersion(), 12755561453219, 'Correct Version.');
$limeTest->is($migration->getName(), 'third_test_migration', 'Correct Name.');

$migrations->next();
$migration = $migrations->current();
$limeTest->isa_ok($migration, 'sfPropelMigration', 'Second Valid Migration.');
$limeTest->is($migration->getVersion(), 12755561493219, 'Correct Version.');
$limeTest->is($migration->getName(), 'second_test_migration', 'Correct Name.');