<?php

namespace Drupal\config_diff_export\Command;

use Drupal\config_diff_export\Differ;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Core\Style\DrupalStyle;
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
   * @var \Drupal\config_diff_export\Differ
   */
  private $differ;

  /**
   * DiffCommand constructor.
   *
   * @param \Drupal\config_diff_export\Differ $differ
   */
  public function __construct(
    Differ $differ
  ) {
    $this->differ = $differ;
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
    $io = new DrupalStyle($input, $output);

    $list = $this->differ->getConfigurationsList();
    if (NULL === $list) {
      $io->info($this->trans('commands.config_diff_export.diff.messages.no_changes'));
      return NULL;
    }

    $io->writeln($list);

    $io->info($this->trans('commands.config_diff_export.diff.messages.success'));
  }
}
