<?php

namespace Drupal\data_fixtures\Form;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\data_fixtures\Common\DataFixturesTypeManager;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the data fixtures export form.
 */
class DataFixturesFullExportForm extends FormBase {

  /**
   * @var \Drupal\data_fixtures\Common\DataFixturesTypeManager
   */
  protected $dataFixturesTypeManager;

  /**
   * @var \Drupal\Core\Config\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $privateTempStore;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * @var \Drupal\Core\Entity\ContentEntityInterface[]
   */
  protected $entities;

  /**
   * DataFixturesFullExportForm constructor.
   *
   * @param \Drupal\data_fixtures\Common\DataFixturesTypeManager $dataFixturesTypeManager
   * @param \Drupal\Core\Entity\Query\QueryFactory               $queryFactory
   * @param \Drupal\user\PrivateTempStoreFactory                 $privateTempStoreFactory
   * @param \Drupal\Core\Session\AccountInterface                $account
   */
  public function __construct(DataFixturesTypeManager $dataFixturesTypeManager, QueryFactory $queryFactory, PrivateTempStoreFactory $privateTempStoreFactory, AccountInterface $account) {
    $this->dataFixturesTypeManager = $dataFixturesTypeManager;
    $this->queryFactory = $queryFactory;
    $this->privateTempStore = $privateTempStoreFactory->get('data_fixtures_multiple_export_confirm');
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.data_fixtures_type'),
      $container->get('entity.query'),
      $container->get('user.private_tempstore'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'data_fixtures_export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [];
    foreach ($this->dataFixturesTypeManager->getDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_definition['type']] = $plugin_definition['label'] . ' (' . $plugin_definition['type'] . ')';
    }

    $form['export'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select the content entity type for which to export template'),
      '#options' => $options,
      '#default_value' => [],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $types = array_filter($form_state->getValue('export', []));
    $entities = [];
    foreach ($types as $type) {
      $entities[$type] = $this->queryFactory->get($type)->execute();
    }
    $this->privateTempStore->set($this->account->id(), $entities);

    $form_state->setRedirect('data_fixtures.export_download');
  }

}
