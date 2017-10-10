<?php

namespace Drupal\config_diff_export\Command;

use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\Core\Config\CachedStorage;
use Drupal\Core\Config\ConfigManager;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\StorageComparer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DiffCommand.
 *
 * @DrupalCommand (
 *     extension="config_diff_export",
 *     extensionType="module"
 * )
 */
class DiffCommand extends ContainerAwareCommand {

  /**
   * @var \Drupal\Core\Config\CachedStorage
   */
  private $configStorage;

  /**
   * @var \Drupal\Core\Config\ConfigManager
   */
  private $configManager;

  /**
   * DiffCommand constructor.
   *
   * @param \Drupal\Core\Config\CachedStorage $configStorage
   * @param \Drupal\Core\Config\ConfigManager $configManager
   */
  public function __construct(
    CachedStorage $configStorage,
    ConfigManager $configManager
  ) {
    $this->configStorage = $configStorage;
    $this->configManager = $configManager;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('config_diff_export:diff')
      ->setDescription($this->trans('commands.config_diff_export.diff.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    global $config_directories;
    $io = new DrupalStyle($input, $output);

    $directory = $config_directories[CONFIG_SYNC_DIRECTORY];
    $fileStorage = new FileStorage($directory);
    $storageComparer = new StorageComparer($this->configStorage, $fileStorage, $this->configManager);

    if (!$storageComparer->createChangelist()->hasChanges()) {
      $output->writeln($this->trans('commands.config.diff.messages.no-changes'));
      return NULL;
    }

    $changeList = [];
    foreach ($storageComparer->getAllCollectionNames() as $collection) {
      $changeList[$collection] = $storageComparer->getChangelist(NULL, $collection);
    }

    foreach ($changeList as $collection => $changes) {
      unset($changes['delete'], $changes['rename']);
      foreach ($changes as $operation => $configs) {
        foreach ($configs as $config) {
          $io->writeln("{$config}.yml");
        }
      }
    }

    $io->info($this->trans('commands.config_diff_export.diff.messages.success'));
  }
}
