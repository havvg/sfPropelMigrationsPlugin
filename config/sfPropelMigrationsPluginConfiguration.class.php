<?php

class sfPropelMigrationsPluginConfiguration extends sfPluginConfiguration
{
  static protected $DEPENDENCIES = array(
    'sfPropelPlugin' => 'The Propel ORM Plugin.',
  );

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $enabledPlugins = $this->configuration->getPlugins();

    foreach (self::$DEPENDENCIES as $pluginName => $whatFor)
    {
      if (!in_array($pluginName, $enabledPlugins))
      {
        throw new sfConfigurationException(sprintf('You must install and enable plugin "%s" which provides %s.', $pluginName, $whatFor));
      }
    }
  }

  /**
   * Returns the top level directory in which the sfPropelMigrationsPlugin is working.
   *
   * @return string
   */
  static public function getMigrationsDir()
  {
    static $migDir = null;

    if (is_null($migDir))
    {
      $default = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'migrations';
      $migDir = sfConfig::get('sf_propelmigrationsplugin_migrationsdir', $default);
    }

    return $migDir;
  }
}