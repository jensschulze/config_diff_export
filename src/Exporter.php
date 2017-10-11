<?php

declare(strict_types=1);

namespace Drupal\config_diff_export;

use Drupal\Component\Serialization\Yaml;
use Drupal\config_diff_export\Exception\ExportException;
use Drupal\Core\Archiver\ArchiveTar;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Class Exporter
 * Jens Schulze, github.com/jensschulze
 */
class Exporter {

  /**
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $storage;

  /**
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * @var \Drupal\config_diff_export\Differ
   */
  private $differ;

  /**
   * @var string
   */
  private $filename = 'config.tar.gz';

  /**
   * Exporter constructor.
   *
   * @param StorageInterface $storage
   * @param ConfigManagerInterface $configManager
   * @param \Drupal\config_diff_export\Differ $differ
   */
  public function __construct(StorageInterface $storage, ConfigManagerInterface $configManager, Differ $differ) {
    $this->storage = $storage;
    $this->configManager = $configManager;
    $this->differ = $differ;
  }

  public function export(?string $archivePath = NULL): string {
    $archiveFilename = $this->getArchiveFullPath($archivePath);
    $archiveTar = new ArchiveTar($archiveFilename, 'gz');

    try {
      // Get raw configuration data without overrides.
      foreach ($this->differ->getConfigurationsList() as $name) {
        $configFile = "$name.yml";

        $configData = $this->configManager->getConfigFactory()
          ->get($name)
          ->getRawData();
        unset($configData['uuid'], $configData['_core']['default_config_hash']);

        $ymlData = Yaml::encode($configData);

        $archiveTar->addString($configFile, $ymlData);
      }
      // We do not fetch config override data here!
    } catch (\Exception $e) {
      throw new ExportException('Export failed!', 0, $e);
    }

    return $archiveFilename;
  }

  public function getArchiveFullPath(?string $archivePath = NULL): string {
    return ($archivePath ?? file_directory_temp()) . DIRECTORY_SEPARATOR . $this->filename;
  }

  public function getFilename(): string {
    return $this->filename;
  }
}