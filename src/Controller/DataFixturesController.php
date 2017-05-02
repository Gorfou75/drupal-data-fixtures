<?php

namespace Drupal\data_fixtures\Controller;

use Drupal\Core\Archiver\ArchiveTar;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\system\FileDownloadController;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Returns responses for data fixtures module routes.
 */
class DataFixturesController implements ContainerInjectionInterface {

  const ARCHIVE_NAME = 'fixtures.tar.gz';

  const COMPRESSION_TYPE = 'gz';

  /**
   * @var \Drupal\Core\Action\ActionInterface[]
   */
  protected $actions;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $privateTempStore;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * The file download controller.
   *
   * @var \Drupal\system\FileDownloadController
   */
  protected $fileDownloadController;

  /**
   * DataFixturesController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface    $entityTypeManager
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   * @param \Drupal\user\PrivateTempStoreFactory              $privateTempStoreFactory
   * @param \Drupal\Core\Session\AccountInterface             $account
   * @param \Drupal\system\FileDownloadController             $fileDownloadController
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, SerializerInterface $serializer, PrivateTempStoreFactory $privateTempStoreFactory, AccountInterface $account, FileDownloadController $fileDownloadController) {
    $this->entityTypeManager = $entityTypeManager;
    $this->serializer = $serializer;
    $this->privateTempStore = $privateTempStoreFactory->get('data_fixtures_multiple_export_confirm');
    $this->account = $account;
    $this->fileDownloadController = $fileDownloadController;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('serializer'),
      $container->get('user.private_tempstore'),
      $container->get('current_user'),
      new FileDownloadController()
    );
  }

  /**
   * Downloads a tarball of the site configuration.
   */
  public function downloadExport() {
    $this->deleteOldArchive();
    $archiver = new ArchiveTar(file_directory_temp() . '/' . self::ARCHIVE_NAME, self::COMPRESSION_TYPE);

    $items = $this->privateTempStore->get($this->account->id());
    foreach ($items as $type => $ids) {
      $entities = $this->entityTypeManager
        ->getStorage($type)
        ->loadMultiple($ids);

      foreach ($entities as $entity) {
        $filename = $this->getExportPath($entity) . '/' . $entity->uuid() . '.json';
        $json = $this->serializer->serialize($entity, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);
        $archiver->addString($filename, $json);
      }
    }

    $request = new Request(['file' => self::ARCHIVE_NAME]);
    return $this->fileDownloadController->download($request, 'temporary');
  }

  /**
   *
   */
  protected function deleteOldArchive() {
    file_unmanaged_delete(file_directory_temp() . '/' . self::ARCHIVE_NAME);
  }

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return string
   */
  protected function getExportPath(EntityInterface $entity) {
    return implode(DIRECTORY_SEPARATOR, [
      'fixtures',
      $entity->getEntityTypeId(),
      $entity->bundle(),
    ]);
  }

}
