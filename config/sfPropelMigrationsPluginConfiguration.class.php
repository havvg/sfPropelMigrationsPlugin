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
}