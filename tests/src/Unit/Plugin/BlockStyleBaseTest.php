<?php

namespace Drupal\Tests\block_style_plugins\Unit\Plugin;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityRepository;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * @coversDefaultClass \Drupal\block_style_plugins\Plugin\BlockStyleBase
 * @group block_style_plugins
 */
class BlockStyleBaseTest extends UnitTestCase
{

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @var \Drupal\block_style_plugins\Plugin\BlockStyleBase
   */
  protected $plugin;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp()
  {
    parent::setUp();

    // stub the Iconset Finder Service
    $entityRepository = $this->prophesize(EntityRepository::CLASS);

    // Form state double
    $this->formState = $this->prophesize(FormStateInterface::CLASS);

    $configuration = [];
    $plugin_id = 'block_style_plugins';
    $plugin_definition['provider'] = 'block_style_plugins';

    $this->plugin = new MockBlockStyleBase(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entityRepository->reveal()
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Tests the defaultStyles method.
   *
   * @covers ::defaultStyles()
   */
  public function testDefaultStyles() {
    $expected = [
      'sample_class' => '',
      'sample_checkbox' => FALSE,
    ];
    $default = $this->plugin->defaultStyles();

    $this->assertArrayEquals($expected, $default);
  }

  /**
   * Tests the formElements method.
   *
   * @see ::formElements()
   */
  public function testFormElements() {
    $form = [];
    $return = $this->plugin->formElements($form, $this->formState->reveal());

    $this->assertArrayEquals([], $return);
  }

  /**
   * Tests the formAlter method.
   *
   * @see ::formAlter()
   */
  public function testFormAlter() {
    $form = ['test'];
    $return = $this->plugin->formAlter($form, $this->formState->reveal());

    $this->assertArrayEquals($form, $return);
  }

  /**
   * Tests the submitForm method.
   *
   * @see ::submitForm()
   */
  public function testSubmitForm() {
    $form = [];
    $return = $this->plugin->submitForm($form, $this->formState->reveal());

    $this->assertNull($return);
  }

  /**
   * Tests the getStyles method.
   *
   * @see ::getStyles()
   */
  public function testGetStyles() {
    $expected = [
      'sample_class' => '',
      'sample_checkbox' => FALSE,
    ];
    $this->plugin->setStyles([]);
    $return = $this->plugin->getStyles();

    $this->assertArrayEquals($expected, $return);
  }

  /**
   * Tests the setStyles method.
   *
   * @see ::setStyles()
   */
  public function testSetStyles() {
    $expected = [
      'sample_class' => '',
      'sample_checkbox' => FALSE,
      'new_key' => 'new_val',
    ];

    $new_styles = ['new_key' => 'new_val'];
    $this->plugin->setStyles($new_styles);
    $return = $this->plugin->getStyles();

    $this->assertArrayEquals($expected, $return);

    // Overwrite styles
    $expected = [
      'sample_class' => 'class_name',
      'sample_checkbox' => TRUE,
    ];

    $this->plugin->setStyles($expected);
    $return = $this->plugin->getStyles();

    $this->assertArrayEquals($expected, $return);
  }

  /**
   * Tests the exclude method.
   *
   * @see ::exclude()
   * @TODO Create a provider so that more combinations can be tested.
   */
  public function testExclude() {
    // stub the blockPlugin
    $blockPlugin = $this->prophesize(PluginInspectionInterface::CLASS);
    $blockPlugin->getPluginId()->willReturn('basic_block');
    $this->plugin->blockPlugin = $blockPlugin->reveal();

    // No exclude options are passed
    $this->plugin->pluginDefinition['exclude'] = FALSE;
    $return = $this->plugin->exclude();
    $this->assertFalse($return);

    // Exclude basic_block
    $this->plugin->pluginDefinition['exclude'] = ['basic_block'];
    $return = $this->plugin->exclude();
    $this->assertTrue($return);

    // Exclude a custom content block
    $this->plugin->pluginDefinition['exclude'] = ['custom_block'];
    $this->plugin->blockContentBundle = 'custom_block';
    $return = $this->plugin->exclude();
    $this->assertTrue($return);
  }

}





































