<?php

namespace Drupal\active_users;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Active users manager.
 */
class ActiveUsersManager {

  /**
   * The user storage handler.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The node storage handler.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Constructs a user list controller.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->userStorage = $entity_manager->getStorage('user');
    $this->nodeStorage = $entity_manager->getStorage('node');
  }

  /**
   * Returns a list of active users.
   *
   * @return \Drupal\user\UserInterface[]
   */
  public function getActiveUsers() {
    $ids = $this->userStorage->getQuery()
      ->condition('status', 1)
      ->condition('node_count', 0, '>')
      ->condition('last_created_node.entity.status', 1)
      ->sort('login', 'DESC')
      ->execute();

    return User::loadMultiple($ids);
  }

  /**
   * Acts when a new node is created.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node being created.
   */
  public function onNodeCreated(NodeInterface $node) {
    $user = $node->getOwner();
    $user->last_created_node = $node;
    $user->node_count = $this->getNodeCount($user);
    $user->save();
  }

  /**
   * Retrieves the number of nodes created by the specified user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The node author.
   *
   * @return int
   *   The number of nodes created.
   */
  protected function getNodeCount(UserInterface $user) {
    $result = $this->nodeStorage->getAggregateQuery()
      ->aggregate('nid', 'COUNT')
      ->condition('uid', $user->id())
      ->execute();

    return $result[0]['nid_count'];
  }

  /**
   * Acts when a new node is deleted.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node being deleted.
   */
  public function onNodeDeleted(NodeInterface $node) {
    $user = $node->getOwner();
    if ($user->last_created_node->target_id == $node->id()) {
      $user->last_created_node = $this->getLastCreatedNode($user);
    }
    $user->node_count = $this->getNodeCount($user);
    $user->save();
  }

  /**
   * Retrieves the last node created by the specified user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The node author.
   *
   * @return int
   *   The last created node identifier.
   */
  protected function getLastCreatedNode(UserInterface $user) {
    $result = $this->nodeStorage->getQuery()
      ->condition('uid', $user->id())
      ->sort('created', 'DESC')
      ->range(0, 1)
      ->execute();

    return reset($result);
  }

}
