<?php

namespace Drupal\data_fixtures\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StreamWrapper\PublicStream;

/**
 * Export as data fixtures
 *
 * @Action(
 *   id = "data_fixtures_export",
 *   label = @Translation("Export as data fixtures")
 * )
 */
class DataFixturesExport extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute(ContentEntityInterface $entity = NULL) {
    /** @var \Symfony\Component\Serializer\Serializer $serializer */
    $serializer = \Drupal::getContainer()->get('serializer');
    $json = $serializer->serialize($entity, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);

    $path = implode(DIRECTORY_SEPARATOR, [
      PublicStream::basePath(),
      $entity->getEntityType()->getLabel(),
      $entity->bundle(),
    ]);

    if (!is_dir($path)) {
      /** @var \Drupal\Core\File\FileSystem $fileSystem */
      $fileSystem = \Drupal::service('file_system');
      $fileSystem->mkdir($path, 0750, TRUE);
    }

    $filename = $path . '/' . $entity->uuid() . '.json';
    file_put_contents($filename, $json);
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = AccessResult::allowedIfHasPermission($account, 'export data fixtures');
    return $return_as_object ? $result : $result->isAllowed();
  }

}
