<?php

namespace Drupal\data_fixtures\Common;

use Drupal\Component\Plugin\FallbackPluginManagerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages data fixtures type plugins.
 *
 * @package Drupal\data_fixtures\Common
 */
class DataFixturesTypeManager extends DefaultPluginManager implements FallbackPluginManagerInterface {

  /**
   * DataFixturesManager constructor.
   *
   * @param \Traversable                                  $namespaces
   * @param \Drupal\Core\Cache\CacheBackendInterface      $cache_backend
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/DataFixtures/DataFixturesType', $namespaces, $module_handler, 'Drupal\pathauto\DataFixturesTypeInterface', 'Drupal\data_fixtures\Annotation\DataFixturesType');

    $this->alterInfo('data_fixtures_info');
    $this->setCacheBackend($cache_backend, 'data_fixtures_types');
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackPluginId($plugin_id, array $configuration = []) {
    return 'broken';
  }
}
