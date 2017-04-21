<?php

namespace Drupal\data_fixtures\Plugin\DataFixtures\DataFixturesType;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityDataFixtures
 * A data fixtures type plugin for content entities.
 *
 * @package Drupal\data_fixtures\Plugin\DataFixtures\DataFixturesType
 *
 * @DataFixturesType(
 *   id = "content_entities",
 *   deriver = "\Drupal\data_fixtures\Plugin\Deriver\EntityDataFixturesDeriver"
 * )
 */
class EntityDataFixtures extends ContextAwarePluginBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * EntityDataFixtures constructor.
   *
   * @param array                                          $configuration
   * @param string                                         $plugin_id
   * @param mixed                                          $plugin_definition
   * @param \Drupal\Core\Extension\ModuleHandlerInterface  $moduleHandler
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $moduleHandler, LanguageManagerInterface $languageManager, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $moduleHandler;
    $this->languageManager = $languageManager;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('language_manager'),
      $container->get('entity_type.manager')
    );
  }

}
