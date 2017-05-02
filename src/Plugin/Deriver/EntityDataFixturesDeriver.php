<?php

namespace Drupal\data_fixtures\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Deriver that exposes content entities as data fixtures type plugins.
 *
 * @package Drupal\data_fixtures\Plugin\Deriver
 */
class EntityDataFixturesDeriver extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs new EntityDataFixturesTypeDeriver.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type) {
      if ($entity_type instanceof ContentEntityTypeInterface) {
        $this->derivatives[$entity_type_id]['label'] = $entity_type->getLabel();
        $this->derivatives[$entity_type_id]['type'] = $entity_type_id;
        $this->derivatives[$entity_type_id]['context'] = [
          $entity_type_id => new ContextDefinition("entity:$entity_type_id", $this->t('@label being aliased', ['@label' => $entity_type->getLabel()])),
        ];
      }
    }

    return $this->derivatives;
  }

}
