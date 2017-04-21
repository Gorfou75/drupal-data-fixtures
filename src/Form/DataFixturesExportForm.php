<?php

namespace Drupal\data_fixtures\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\data_fixtures\Common\DataFixturesTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the data fixtures export form.
 */
class DataFixturesExportForm extends FormBase {

  /**
   * @var \Drupal\data_fixtures\Common\DataFixturesTypeManager
   */
  protected $dataFixturesTypeManager;

  /**
   * DataFixturesExportForm constructor.
   *
   * @param \Drupal\data_fixtures\Common\DataFixturesTypeManager $dataFixturesTypeManager
   */
  public function __construct(DataFixturesTypeManager $dataFixturesTypeManager) {
    $this->dataFixturesTypeManager = $dataFixturesTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.data_fixtures_type')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'data_fixtures_export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [];
    foreach ($this->dataFixturesTypeManager->getDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = $plugin_definition['label'];
    }

    $form['export'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select the content entity type for which to export template'),
      '#options' => $options,
      '#default_value' => [],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}
