<?php

namespace Drupal\data_fixtures\Common;

use Drupal\Core\Entity\EntityManager;

/**
 * Interface contract for fixture classes to implement.
 *
 * @package Drupal\data_fixtures\Common
 */
interface FixtureInterface {

  /**
   * Load data fixtures with the passed EntityManager
   *
   * @param \Drupal\Core\Entity\EntityManager $manager
   *
   * @return mixed
   */
  public function load(EntityManager $manager);

  /**
   * Export data fixtures with the passed EntityManager
   *
   * @param \Drupal\Core\Entity\EntityManager $manager
   *
   * @return mixed
   */
  public function export(EntityManager $manager);

}
