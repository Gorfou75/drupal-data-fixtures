<?php

namespace Drupal\data_fixtures\Form;

use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the data fixtures export template form.
 */
class DataFixturesExportTemplateForm extends FormBase {

  /**
   * The alias type manager.
   *
   * @var \Drupal\pathauto\AliasTypeManager
   */
  protected $entityTypeManager;

  /**
   * DataFixturesExportTemplateForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'data_fixtures_export_template_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#export'] = [];

    $contentTypes = [];
    $entityTypeDefinitions = $this->entityTypeManager->getDefinitions();
    /* @var $definition \Drupal\Core\Entity\EntityTypeInterface */
    foreach ($entityTypeDefinitions as $definition) {
      if ($definition instanceof ContentEntityType) {
        $contentTypes[$definition->get('machine_name')] = $definition->getLabel();
      }
    }


    $options = [];
    foreach ($this->entityTypeManager->getVisibleDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = $plugin_definition['label'];
    }

    $form['export'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select the content entity type for which to export template'),
      '#options' => $options,
      '#default_value' => [],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Export'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}
