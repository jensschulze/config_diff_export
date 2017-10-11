<?php

namespace Drupal\config_diff_export\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ExportForm.
 */
class ExportForm extends FormBase {

//  /**
//   * {@inheritdoc}
//   */
//  protected function getEditableConfigNames(): array {
//    return [
//      'config_diff_export.export',
//    ];
//  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state):array {
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('config_diff_export.export_controller_export_download');
  }

}
