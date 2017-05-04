<?php

namespace Drupal\Tests\block_style_plugins\Unit\Plugin;

use Drupal\block_style_plugins\Plugin\BlockStyleBase;

class MockBlockStyleBase extends BlockStyleBase {

  // This class is empty because we need it to unit test base methods in the
  // Abstract BlockStyleBase class

  /**
   * The plugin implementation definition.
   *
   * @var array
   */
  public $pluginDefinition;

  /**
   * Bundle type for 'Block Content' blocks.
   *
   * @var string
   */
  public $blockContentBundle;

  /**
   * Plugin instance for the Block being configured.
   *
   * @var object
   */
  public $blockPlugin;

  /**
   * {@inheritdoc}
   */
  public function defaultStyles() {
    return [
      'sample_class' => '',
      'sample_checkbox' => FALSE,
    ];
  }

}
