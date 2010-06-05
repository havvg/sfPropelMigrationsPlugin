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
   * The migration process itself.
   *
   * @return bool
   */
  protected function up()
  {
    $up
  }

  /**
   * The process to revert this migration.
   *
   * @param bool \$failed A flag whether the down is called because of a failed up. Defaults to false (normal down action).
   *
   * @return bool
   */
  protected function down(\$failed = false)
  {
    $down
  }
}
EOF;

    return $skeleton;
  }
}