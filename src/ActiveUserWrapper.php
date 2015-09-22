<?php

/**
 * @file
 * Contains \Drupal\active_user\ActiveUserWrapper.
 */

namespace Drupal\active_user;

use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * Common interface for active user wrappers.
 */
class ActiveUserWrapper implements ActiveUserWrapperInterface {

  /**
   * The wrapped user entity.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * Constructs a new active user wrapper.
   *
   * @param \Drupal\user\UserInterface $user
   *   The wrapped user entity.
   */
  public function __construct(UserInterface $user) {
    $this->user = $user;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCreatedNode() {
    return $this->user->get('active_user_last_created_node')->__get('entity');
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCreatedNodeId() {
    return $this->user->get('active_user_last_created_node')->__get('target_id');
  }

  /**
   * {@inheritdoc}
   */
  public function setLastCreatedNode(NodeInterface $node) {
    $this->user->set('active_user_last_created_node', $node);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeCount() {
    return $this->user->get('active_user_node_count')->__get('value');
  }

  /**
   * {@inheritdoc}
   */
  public function setNodeCount($count) {
    $this->user->set('active_user_node_count', $count);
    return $this;
  }

}
