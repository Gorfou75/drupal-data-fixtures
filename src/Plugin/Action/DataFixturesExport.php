<?php

namespace Drupal\data_fixtures\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Export as data fixtures
 *
 * @Action(
 *   id = "data_fixtures_export",
 *   label = @Translation("Export as data fixtures"),
 *   confirm_form_route_name = "data_fixtures.export_download"
 * )
 */
class DataFixturesExport extends ActionBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $privateTempStore;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * DataFixturesExport constructor.
   *
   * @param array                                 $configuration
   * @param string                                $plugin_id
   * @param mixed                                 $plugin_definition
   * @param \Drupal\user\PrivateTempStoreFactory  $privateTempStoreFactory
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PrivateTempStoreFactory $privateTempStoreFactory, AccountInterface $account) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->privateTempStore = $privateTempStoreFactory->get('data_fixtures_multiple_export_confirm');
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('user.private_tempstore'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ContentEntityInterface $entity = NULL) {
    $this->executeMultiple([$entity]);
  }

  /**
   * Executes the plugin for an array of objects.
   *
   * @param ContentEntityInterface[] $entities
   *   An array of entities.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function executeMultiple(array $entities) {
    $values = [];
    /** @var ContentEntityInterface $entity */
    foreach ($entities as $entity) {
      $values[$entity->getEntityTypeId()][] = $entity->id();
    }
    $this->privateTempStore->set($this->account->id(), $values);
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = AccessResult::allowedIfHasPermission($account, 'export data fixtures');
    return $return_as_object ? $result : $result->isAllowed();
  }

}
