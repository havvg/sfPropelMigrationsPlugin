<?php

class sfPropelMigrationSkeleton
{
  static public function getSkeleton($version, $up = null, $down = null)
  {
    if (!intval($version))
    {
      throw new InvalidArgumentException(sfPropelMigration::EXCEPTION_INVALID_VERSION);
    }

    $skeleton = <<<EOF
<?php

class sfMigration_{$version} extends sfMigration
{
  /**
   * Execute this migration.
   */
  public function up()
  {
    $up
  }

  /**
   * Revert this migration.
   */
  public function down()
  {
    $down
  }
}
EOF;

    return $skeleton;
  }
}