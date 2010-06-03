<?php
require_once(dirname(__FILE__) . '/../bootstrap/unit.php');

# load fixtures of this plugin
$propelData->loadData(sfConfig::get('sf_plugins_dir') . '/sfPropelMigrationsPlugin/data/fixtures');

$limeTest = new lime_test(1, new lime_output_color());

$schemaVersion = SchemaVersionPeer::retrieveByPK(12755561356551, 'propel');
$limeTest->isa_ok($schemaVersion, 'SchemaVersion', 'Found SchemaVersion');