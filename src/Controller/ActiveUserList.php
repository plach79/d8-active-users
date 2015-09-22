<?php

/**
 * @file
 * Contains \Drupal\active_user\ActiveUserList.
 */

namespace Drupal\active_user\Controller;

use Drupal\active_user\ActiveUserManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Active user list controller.
 */
class ActiveUserList extends ControllerBase {

  /**
   * The active users manager.
   *
   * @var \Drupal\active_user\ActiveUserManagerInterface
   */
  protected $manager;

  /**
   * Constructs a user list controller.
   *
   * @param \Drupal\active_user\ActiveUserManagerInterface $manager
   *   The active users manager.
   */
  public function __construct(ActiveUserManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('active_user.manager'));
  }

  /**
   * Returns the active user list output.
   *
   * @return array
   *   A renderable array.
   */
  public function view() {
    $rows = [];

    foreach ($this->manager->getActiveUserList() as $user) {
      $active_user = $this->manager->getActiveUser($user);
      $rows[]['data'] = [
        $user->label(),
        $active_user->getNodeCount(),
        $active_user->getLastCreatedNode()->label(),
      ];
    }

    return [
      '#theme' => 'table',
      '#header' => [$this->t('User'), $this->t('Node count'), $this->t('Last created node')],
      '#rows' => $rows,
    ];
  }

}
