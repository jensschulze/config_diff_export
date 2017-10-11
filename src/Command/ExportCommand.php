<?php

namespace Drupal\config_diff_export\Command;

use Drupal\config_diff_export\Exporter;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Core\Style\DrupalStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportCommand.
 *
 * @DrupalCommand (
 *     extension="config_diff_export",
 *     extensionType="module"
 * )
 */
class ExportCommand extends ContainerAwareCommand {

  /**
   * @var \Drupal\config_diff_export\Exporter
   */
  private $exporter;

  public function __construct(Exporter $exporter) {
    $this->exporter = $exporter;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('config_diff_export:export')
      ->setAliases(['cdee'])
      ->setDescription($this->trans('commands.config_diff_export.export.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $filename = $this->exporter->export();

    $io->info(sprintf($this->trans('commands.config_diff_export.export.messages.success'), $filename));
  }
}
