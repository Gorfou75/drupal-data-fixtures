<?php

namespace Drupal\data_fixtures\Form;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * Defines the data fixtures synchronize form.
 */
class DataFixturesSyncForm extends FormBase {

  const DATA_FIXTURES_DIRECTORY = '/config/fixtures';

  const DATA_FIXTURES_EXTENSION = '*.json';

  /**
   * @var string
   */
  private $drupalRoot;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * DataFixturesSyncForm constructor.
   *
   * @param string                                        $root
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   */
  public function __construct($root, ModuleHandlerInterface $moduleHandler) {
    $this->drupalRoot = $root;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('app.root'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'data_fixtures_sync_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($this->moduleHandler->getModuleList() as $name => $extension) {
      $path = $this->getDataFixturesDirectoryPath($extension);
      if (!is_dir($path)) {
        continue;
      }

      $form['list'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Extension'),
          $this->t('Operations'),
        ],
      ];
      $route_name = 'config.diff_collection';
      $route_options = [
        'source_name' => $name,
        'collection' => $name,
      ];

      $links['view_diff'] = [
        'title' => $this->t('View fixtures'),
        'url' => Url::fromRoute($route_name, $route_options),
        'attributes' => [
          'class' => ['use-ajax'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => json_encode([
            'width' => 1000,
          ]),
        ],
      ];

      $form['list']['#rows'][] = [
        'title' => $this->moduleHandler->getName($name),
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => $links,
          ],
        ],
      ];
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import all'),
    ];

    // Add the AJAX library to the form for dialog support.
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * @param \Drupal\Core\Extension\Extension $extension
   *
   * @return string
   */
  protected function getDataFixturesDirectoryPath(Extension $extension) {
    return $fixturesPath = $this->drupalRoot . '/' . $extension->getPath() . self::DATA_FIXTURES_DIRECTORY;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

  /**
   * Recursively scans an extension directory for data fixtures.
   *
   * @param string $dir
   *
   * @return array
   */
  protected function scanDataFixturesDirectory($dir) {
    $finder = new Finder();
    $finder->files()->name(self::DATA_FIXTURES_EXTENSION)->in($dir);

    $files = [];
    foreach ($finder as $file) {
      list($entity, $bundle,) = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
      $files[$file->getFilename()] = [
        'entity' => $entity,
        'bundle' => $bundle,
      ];
    }

    return $files;
  }
}
