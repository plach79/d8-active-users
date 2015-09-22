<?php

/**
 * @file
 * Contains \Drupal\active_user\ActiveUserManagerInterface.
 */

namespace Drupal\active_user;

use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * Common interface for active user managers.
 */
interface ActiveUserManagerInterface {

  /**
   * Returns a user wrapper for the current user.
   *
   * @param \Drupal\user\UserInterface $user
   *
   * @return \Drupal\active_user\ActiveUserWrapperInterface
   */
  public function getActiveUser(UserInterface $user);

  /**
   * Returns a list of active users.
   *
   * @return \Drupal\user\UserInterface[]
   */
  public function getActiveUserList();

  /**
   * Acts when a new node is created.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node being created.
   */
  public function onNodeCreated(NodeInterface $node);

  /**
   * Acts when a new node is deleted.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node being deleted.
   */
  public function onNodeDeleted(NodeInterface $node);

}
