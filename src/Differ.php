<?php

declare(strict_types=1);

namespace Drupal\config_diff_export;

use Drupal\Core\Config\CachedStorage;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\StorageComparer;

/**
 * Class Differ
 * Jens Schulze, github.com/jensschulze
 */
class Differ {

  /**
   * @var \Drupal\Core\Config\CachedStorage
   */
  private $configStorage;

  /**
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  private $configManager;

  /**
   * Differ constructor.
   *
   * @param \Drupal\Core\Config\CachedStorage $configStorage
   * @param \Drupal\Core\Config\ConfigManagerInterface $configManager
   */
  public function __construct(
    CachedStorage $configStorage,
    ConfigManagerInterface $configManager
  ) {
    $this->configStorage = $configStorage;
    $this->configManager = $configManager;
  }

  public function getConfigurationsList(): ?array {
    global $config_directories;

    $directory = $config_directories[CONFIG_SYNC_DIRECTORY];
    $fileStorage = new FileStorage($directory);
    $storageComparer = new StorageComparer($this->configStorage, $fileStorage, $this->configManager);

    if (!$storageComparer->createChangelist()->hasChanges()) {
      return NULL;
    }

    $changeList = [];
    foreach ($storageComparer->getAllCollectionNames() as $collection) {
      $changeList[$collection] = $storageComparer->getChangelist(NULL, $collection);
    }

    $list = [];
    foreach ($changeList as $collection => $changes) {
      unset($changes['delete'], $changes['rename']);
      foreach ($changes as $operation => $configs) {
        foreach ($configs as $name) {
          $list[] = "$name";
        }
      }
    }

    return $list;
  }
}