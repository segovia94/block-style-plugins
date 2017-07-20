<?php

namespace Drupal\Tests\block_style_plugins\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Test of the Block Style Plugins discovery integration.
 *
 * @group block_style_plugins
 */
class PluginDiscoveryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['block_style_plugins'];

  /**
 * Make sure that plugins are discovered.
 */
  public function testPluginDiscovery() {
    $plugin_manager = $this->container->get('plugin.manager.block_style.processor');
    $style_plugins = $plugin_manager->getDefinitions();

    $expected = [
      'sample_block_style' => [
        'exclude' => [],
        'include' => [
          'block_plugin_base_id'
        ],
        'id' => 'sample_block_style',
        'label' => 'Sample Block Style',
        'class' => 'Drupal\block_style_plugins\Plugin\BlockStyle\SampleBlockStyle',
        'provider' => 'block_style_plugins'
      ]
    ];
    $this->assertEquals($expected, $style_plugins);
  }

  /**
   * Make sure that plugins are discovered.
   */
  public function testInstanceCreation() {
    $plugin_manager = $this->container->get('plugin.manager.block_style.processor');

    $style_plugin = $plugin_manager->createInstance('sample_block_style');

    $this->assertInstanceOf('Drupal\block_style_plugins\Plugin\BlockStyleInterface', $style_plugin);
  }

}
