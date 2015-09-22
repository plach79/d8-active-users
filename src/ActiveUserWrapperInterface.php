<?php

/**
 * @file
 * Contains \Drupal\active_user\ActiveUserWrapperInterface.
 */

namespace Drupal\active_user;

use Drupal\node\NodeInterface;

/**
 * Common interface for active user wrappers.
 */
interface ActiveUserWrapperInterface {

  /**
   * Returns the last created node.
   *
   * @return \Drupal\node\NodeInterface
   *   A node entity.
   */
  public function getLastCreatedNode();

  /**
   * Returns the last created node id.
   *
   * Used when the node is deleted and so we cannot load it.
   *
   * @return int
   *   A node identifier.
   */
  public function getLastCreatedNodeId();

  /**
   * Sets the last created node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node entity.
   *
   * @return static
   */
  public function setLastCreatedNode(NodeInterface $node);

  /**
   * Returns the number of nodes created by the user.
   *
   * @return int
   *   The node count.
   */
  public function getNodeCount();

  /**
   * Sets the number of nodes created by the user.
   *
   * @param int $count
   *   The node count.
   *
   * @return static
   */
  public function setNodeCount($count);

}
