<?php

namespace Drupal\data_fixtures\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StreamWrapper\PublicStream;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Export as data fixtures
 *
 * @Action(
 *   id = "data_fixtures_export",
 *   label = @Translation("Export as data fixtures")
 * )
 */
class DataFixturesExport extends ActionBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * DataFixturesExport constructor.
   *
   * @param array                                             $configuration
   * @param string                                            $plugin_id
   * @param mixed                                             $plugin_definition
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   * @param \Drupal\Core\File\FileSystemInterface             $fileSystem
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SerializerInterface $serializer, FileSystemInterface $fileSystem) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->serializer = $serializer;
    $this->fileSystem = $fileSystem;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('serializer'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ContentEntityInterface $entity = NULL) {
    $path = $this->getExportPath($entity);
    $this->ensureExportPath($path);

    $filename = $path . '/' . $entity->uuid() . '.json';

    $json = $this->serializer->serialize($entity, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);
    file_put_contents($filename, $json);
  }

  /**
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return string
   */
  protected function getExportPath(ContentEntityInterface $entity) {
    return implode(DIRECTORY_SEPARATOR, [
      PublicStream::basePath(),
      $entity->getEntityType()->getLabel(),
      $entity->bundle(),
    ]);
  }

  /**
   * @param string $path
   */
  protected function ensureExportPath($path = NULL) {
    if ($path && !is_dir($path)) {
      $this->fileSystem->mkdir($path, 0750, TRUE);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = AccessResult::allowedIfHasPermission($account, 'export data fixtures');
    return $return_as_object ? $result : $result->isAllowed();
  }
}
