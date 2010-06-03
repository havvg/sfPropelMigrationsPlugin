<?php
require_once(dirname(__FILE__) . '/../bootstrap/task.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfPropelMigrationsPlugin/data/fixtures');

$limeTest = new lime_test(7, new lime_output_color());

$fixturesMigrationsDir = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfPropelMigrationsPlugin' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'no-migrations' . DIRECTORY_SEPARATOR;
sfConfig::set('sf_propelmigrationsplugin_migrationsdir', $fixturesMigrationsDir);
$limeTest->is(sfPropelMigrationsPluginConfiguration::getMigrationsDir(), $fixturesMigrationsDir, 'Configured MigrationsDirectory.');

$task = new sfPropelInitMigrationTask($dispatcher, $formatter);

chmod($fixturesMigrationsDir, 0555);
$task->run(array('"Test Migration"'), array());

$logs = $logger->getLogEntries();
$limeTest->like($logs[1], '/' . sfPropelMigrator::EXCEPTION_MIGRATION_DIR_NOT_WRITABLE . '/', 'Directory currently not writable.');

chmod($fixturesMigrationsDir, 0775);
$task->run(array('"Test Migration"'), array());

$logs = $logger->getLogEntries();
$limeTest->like($logs[3], '/file\+.*Test_Migration.php/', 'File was written.');

$propelMigrator = new sfPropelMigrator();
$propelMigration = $propelMigrator->getMigrations()->current();
$limeTest->is(count($propelMigrator->getMigrations()), 1, 'Found new migration.');
$limeTest->is($propelMigration->getName(), 'Test_Migration', 'Correct name.');
$content = file_get_contents($propelMigration->getFullFilename());
$limeTest->is($content, sfPropelMigrationSkeleton::getSkeleton($propelMigration->getVersion()), 'Skeleton was written correctly.');

require_once($propelMigration->getFullFilename());
$className = 'sfMigration_' . $propelMigration->getVersion();
$migration = new $className;
$limeTest->isa_ok($migration, $className, 'Created sfMigration instance.');

// clean up the mess, we did :-)
$files = sfFinder::type('file')->name('/^\d{14}.*\.php$/')->maxdepth(0)->in($fixturesMigrationsDir . DIRECTORY_SEPARATOR . 'Test_Migration' . DIRECTORY_SEPARATOR);
$task->getFilesystem()->remove($files);
$task->getFilesystem()->remove($fixturesMigrationsDir . DIRECTORY_SEPARATOR . 'Test_Migration' . DIRECTORY_SEPARATOR);