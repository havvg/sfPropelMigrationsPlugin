<?php
require_once(dirname(__FILE__) . '/../bootstrap/unit.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfPropelMigrationsPlugin/data/fixtures');

$limeTest = new lime_test(15, new lime_output_color());

$propelMigrations = new sfPropelMigrations();
$limeTest->isa_ok($propelMigrations, 'sfPropelMigrations', 'Created sfPropelMigrations.');
$limeTest->is(count($propelMigrations), 0, 'Empty.');

$migrations = array();
$migrations[] = new sfPropelMigration(1, 'first');
$limeTest->isa_ok($migrations[0], 'sfPropelMigration', 'Created sfPropelMigration.');

$migrations[] = new sfPropelMigration(2, 'second');
$limeTest->isa_ok($migrations[1], 'sfPropelMigration', 'Created sfPropelMigration.');

$propelMigrations = new sfPropelMigrations($migrations);
$limeTest->isa_ok($propelMigrations, 'sfPropelMigrations', 'Created sfPropelMigrations.');
$limeTest->is(count($propelMigrations), 2, 'Found two migrations.');

$propelMigrations->addMigration(new sfPropelMigration(4, 'fourth'));
$limeTest->is(count($propelMigrations), 3, 'Found three migrations.');

$propelMigrations->addMigration(new sfPropelMigration(3, 'third'));
$limeTest->is(count($propelMigrations), 4, 'Found three migrations.');

$propelMigrations->rewind();
$limeTest->is($propelMigrations->current()->getVersion(), 1, 'First.');
$propelMigrations->next();
$limeTest->is($propelMigrations->current()->getVersion(), 2, 'Second.');
$propelMigrations->next();
$limeTest->is($propelMigrations->current()->getVersion(), 3, 'Third.');
$propelMigrations->next();
$limeTest->is($propelMigrations->current()->getVersion(), 4, 'Fourth.');
$propelMigrations->next();
$limeTest->is($propelMigrations->valid(), false, 'Iterator end.');

try
{
  $propelMigrations = new sfPropelMigrations(array(false));
  $limeTest->fail('InvalidArgumentException not thrown.');
}
catch (InvalidArgumentException $e)
{
  $limeTest->pass('InvalidArgumentException caught.');
  $limeTest->is($e->getMessage(), sfPropelMigrations::EXCEPTION_INVALID_LIST, 'Correct Exception.');
}