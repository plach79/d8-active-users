<?php

namespace Drupal\active_users\Controller;

use Drupal\Component\Utility\String;
use Drupal\Core\Controller\ControllerBase;
use Drupal\active_users\ActiveUsersManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Active users list controller.
 */
class ActiveUsersList extends ControllerBase {

  /**
   * The active users manager.
   *
   * @var \Drupal\active_users\ActiveUsersManager
   */
  protected $manager;

  /**
   * Constructs a user list controller.
   *
   * @param \Drupal\active_users\ActiveUsersManager $manager
   *   The active users manager.
   */
  public function __construct(ActiveUsersManager $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('active_users.manager'));
  }

  /**
   * Returns the active user list output.
   *
   * @return array
   *   A renderable array.
   */
  public function view() {
    $rows = [];

    foreach ($this->manager->getActiveUsers() as $user) {
      $rows[]['data'] = [
        String::checkPlain($user->label()),
        intval($user->node_count->value),
        String::checkPlain($user->last_created_node->entity->label()),
      ];
    }

    return [
      '#theme' => 'table',
      '#header' => [$this->t('User'), $this->t('Node count'), $this->t('Last created node')],
      '#rows' => $rows,
    ];
  }

}
