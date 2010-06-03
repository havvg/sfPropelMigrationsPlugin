<?php

class sfPropelMigration
{
  const EXCEPTION_INVALID_VERSION = 'The given version number is invalid.';

  const EXCEPTION_INVALID_NAME = 'The given name is invalid.';

  const EXCEPTION_INVALID_FILENAME = 'The given filename is invalid.';

  /**
   * The unique version number of this migration.
   *
   * @var int
   */
  protected $version;

  /**
   * The unique name of this migration.
   *
   * @var string
   */
  protected $name;

  /**
   * Constructor
   *
   * @throws InvalidArgumentException
   *
   * @param int $version
   * @param string $name
   *
   * @return void
   */
  public function __construct($version, $name)
  {
    if (!intval($version))
    {
      throw new InvalidArgumentException(self::EXCEPTION_INVALID_VERSION);
    }

    $name = $this->sanitizeName($name);
    if (!is_string($name) || empty($name))
    {
      throw new InvalidArgumentException(self::EXCEPTION_INVALID_NAME);
    }

    $this->version = $version;
    $this->name = $name;
  }

  /**
   * Returns the version number of this migration.
   *
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }

  /**
   * Returns the name of this migration.
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Sanitizes the given name to be a valid name for a migration.
   *
   * @param string $name
   *
   * @return string
   */
  public function sanitizeName($name)
  {
    if (!is_string($name) || empty($name))
    {
      throw new InvalidArgumentException(self::EXCEPTION_INVALID_NAME);
    }

    return preg_replace('/[^a-zA-Z0-9]/', '_', $name);
  }

  /**
   * Returns the filename of this migration.
   *
   * @return string
   */
  public function getFilename()
  {
    return $this->getName() . DIRECTORY_SEPARATOR . $this->getVersion() . '_' . $this->getName() . '.php';
  }

  /**
   * Returns the full path to the file of this migration.
   *
   * @return string
   */
  public function getFullFilename()
  {
    return sfPropelMigrationsPluginConfiguration::getMigrationsDir() . DIRECTORY_SEPARATOR . $this->getFilename();
  }

  /**
   * Returns the dirname of the directory of this migration.
   *
   * @return string
   */
  public function getDirname()
  {
    return dirname($this->getFullFilename());
  }

  /**
   * Creates an instance of sfPropelMigration from a given filename.
   *
   * @throws InvalidArgumentException
   *
   * @param string $filename
   *
   * @return sfPropelMigration
   */
  static public function createInstanceFromFilename($filename)
  {
    if (!is_string($filename) || !file_exists($filename))
    {
      throw new InvalidArgumentException(self::EXCEPTION_INVALID_FILENAME);
    }

    $filename = explode(DIRECTORY_SEPARATOR, $filename);
    $c = count($filename);

    if ($c < 2)
    {
      throw new InvalidArgumentException(self::EXCEPTION_INVALID_FILENAME);
    }

    $name = $filename[$c - 2];
    $version = intval(substr($filename[$c - 1], 0, 14));

    return new self($version, $name);
  }
}