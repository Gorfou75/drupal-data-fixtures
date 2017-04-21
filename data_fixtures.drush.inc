<?php

/**
 * @file
 * Contains drush commands for the Data fixtures module.
 */


/**
 * Implements hook_drush_command().
 */
function data_fixtures_drush_command() {
  $items['data-fixture-export'] = [
    'description' => 'Export content as data fixtures.',
    'arguments' => [],
    'required-arguments' => 0,
    'aliases' => ['dfe'],
    'options' => [],
    'drupal dependencies' => [
      'serialization',
    ],
  ];
  $items['data-fixture-load'] = [
    'description' => 'Load data fixtures.',
    'arguments' => [],
    'required-arguments' => 0,
    'aliases' => ['dfl'],
    'options' => [],
    'drupal dependencies' => [
      'serialization',
    ],
  ];

  return $items;
}

/**
 * Drush callback for data-fixture-export.
 */
function drush_data_fixture_export() {

}

/**
 * Drush callback for data-fixture-load.
 */
function drush_data_fixture_load() {

}
