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