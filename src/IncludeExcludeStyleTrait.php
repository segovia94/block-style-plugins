<?php

namespace Drupal\block_style_plugins;

/**
 * Provides a helper for determining whether to include or exclude a plugin.
 */
trait IncludeExcludeStyleTrait {

  /**
   * Determine whether a style should be allowed.
   *
   * @param string $plugin_id
   *   The ID of the block being checked.
   * @param array $plugin_definition
   *   A list of definitions for a block_style_plugin which could have 'include'
   *   or 'exclude' as keys.
   *
   * @return bool
   *   Return True if the block should show the styles.
   */
  public function allowStyles($plugin_id, array $plugin_definition) {
    if ($this->includeOnly($plugin_id, $plugin_definition) && !$this->exclude($plugin_id, $plugin_definition)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Exclude styles from appearing on blocks.
   *
   * Determine if configuration should be excluded from certain blocks when a
   * block plugin id or block content type is passed from a plugin.
   *
   * @param string $plugin_id
   *   The ID of the block being checked.
   * @param array $plugin_definition
   *   A list of definitions for a block_style_plugins which could have the key
   *   'exclude' set as a list of block plugin ids to disallow.
   *
   * @return bool
   *   Return True if the current block should not get the styles.
   */
  public function exclude($plugin_id, array $plugin_definition) {
    $list = [];

    if (isset($plugin_definition['exclude'])) {
      $list = $plugin_definition['exclude'];
    }

    if (!empty($list) && (in_array($plugin_id, $list))) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Only show styles on specific blocks.
   *
   * Determine if configuration should be only included on certain blocks when a
   * block plugin id or block content type is passed from a plugin.
   *
   * @param string $plugin_id
   *   The ID of the block being checked.
   * @param array $plugin_definition
   *   A list of definitions for a block_style_plugins which could have the key
   *   'include' set as a list of block plugin ids to allow.
   *
   * @return bool
   *   Return True if the current block should only get the styles.
   */
  public function includeOnly($plugin_id, array $plugin_definition) {
    $list = [];

    if (isset($plugin_definition['include'])) {
      $list = $plugin_definition['include'];
    }

    if (empty($list) || (in_array($plugin_id, $list))) {
      return TRUE;
    }

    return FALSE;
  }

}
