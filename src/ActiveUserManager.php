<?php

/**
 * @file
 * Contains \Drupal\active_user\ActiveUserManager.
 */

namespace Drupal\active_user;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Active user manager.
 */
class ActiveUserManager implements ActiveUserManagerInterface {

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
   * {@inheritdoc}
   */
  public function getActiveUser(UserInterface $user) {
    return new ActiveUserWrapper($user);
  }

  /**
   * {@inheritdoc}
   */
  public function getActiveUserList() {
    $ids = $this->userStorage->getQuery()
      ->condition('status', 1)
      ->condition('active_user_node_count', 0, '>')
      ->condition('active_user_last_created_node.entity.status', 1)
      ->sort('login', 'DESC')
      ->execute();

    return User::loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public function onNodeCreated(NodeInterface $node) {
    $user = $node->getOwner();
    $this->getActiveUser($user)
      ->setLastCreatedNode($node)
      ->setNodeCount($this->getNodeCount($user));
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
   * {@inheritdoc}
   */
  public function onNodeDeleted(NodeInterface $node) {
    $user = $node->getOwner();
    $active_user = $this->getActiveUser($user);
    if ($active_user->getLastCreatedNodeId() == $node->id() && ($last = $this->getLastCreatedNode($user))) {
      $active_user->setLastCreatedNode($last);
    }
    $active_user->setNodeCount($this->getNodeCount($user));
    $user->save();
  }

  /**
   * Retrieves the last node created by the specified user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The node author.
   *
   * @return \Drupal\node\NodeInterface
   *   The last created node.
   */
  protected function getLastCreatedNode(UserInterface $user) {
    $result = $this->nodeStorage->getQuery()
      ->condition('uid', $user->id())
      ->sort('created', 'DESC')
      ->range(0, 1)
      ->execute();

    return Node::load(reset($result));
  }

}
