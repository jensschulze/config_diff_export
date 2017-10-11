<?php

namespace Drupal\config_diff_export\Controller;

use Drupal\config_diff_export\Exporter;
use Drupal\Core\Controller\ControllerBase;
use Drupal\system\FileDownloadController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExportController.
 */
class ExportController extends ControllerBase {

  /**
   * Drupal\config_diff_export\Exporter definition.
   *
   * @var \Drupal\config_diff_export\Exporter
   */
  private $exporter;

  /**
   * @var \Drupal\system\FileDownloadController
   */
  private $fileDownloadController;

  /**
   * Constructs a new ExportController object.
   *
   * @param \Drupal\config_diff_export\Exporter $exporter
   * @param \Drupal\system\FileDownloadController $fileDownloadController
   */
  public function __construct(Exporter $exporter, FileDownloadController $fileDownloadController) {
    $this->exporter = $exporter;
    $this->fileDownloadController = $fileDownloadController;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
   * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config_diff_export.exporter'),
      new FileDownloadController()
    );
  }

  /**
   * Export_archive.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function export_archive() {
    $archiveFile = $this->exporter->getArchiveFullPath();
    file_unmanaged_delete($archiveFile);

    $this->exporter->export();

    $request = new Request(['file' => $this->exporter->getFilename()]);
    return $this->fileDownloadController->download($request, 'temporary');
  }
}
