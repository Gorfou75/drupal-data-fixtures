<?php

namespace Drupal\data_fixtures\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the data fixtures synchronize form.
 */
class DataFixturesSyncForm extends FormBase {

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
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}
