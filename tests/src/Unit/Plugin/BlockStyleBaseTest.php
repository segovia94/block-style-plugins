<?php

namespace Drupal\Tests\block_style_plugins\Unit\Plugin;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityRepository;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * @coversDefaultClass \Drupal\block_style_plugins\Plugin\BlockStyleBase
 * @group block_style_plugins
 */
class BlockStyleBaseTest extends UnitTestCase
{

  /**
   * @var \Drupal\Core\Entity\EntityRepository
   */
  protected $entityRepository;

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
    $this->entityRepository = $this->prophesize(EntityRepository::CLASS);

    // Form state double
    $this->formState = $this->prophesize(FormStateInterface::CLASS);

    $configuration = [];
    $plugin_id = 'block_style_plugins';
    $plugin_definition['provider'] = 'block_style_plugins';

    $this->plugin = new MockBlockStyleBase(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $this->entityRepository->reveal()
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Tests the create method.
   *
   * @see ::create()
   */
  public function testCreate() {
    $configuration = [];
    $plugin_id = 'block_style_plugins';
    $plugin_definition['provider'] = 'block_style_plugins';

    $container = $this->prophesize(ContainerInterface::CLASS);
    $container->get('entity.repository')->willReturn($this->entityRepository->reveal());

    $instance = MockBlockStyleBase::create(
      $container->reveal(),
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $this->assertInstanceOf('Drupal\block_style_plugins\Plugin\BlockStyleInterface', $instance);
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

    // Exclude a block that is not the current one
    $this->plugin->pluginDefinition['exclude'] = ['wrong_block'];
    $return = $this->plugin->exclude();
    $this->assertFalse($return);

    // Exclude a custom content block
    $this->plugin->pluginDefinition['exclude'] = ['custom_block'];
    $this->plugin->blockContentBundle = 'custom_block';
    $return = $this->plugin->exclude();
    $this->assertTrue($return);

    // Exclude a custom content block that is not the current block
    $this->plugin->pluginDefinition['exclude'] = ['wrong_custom_block'];
    $this->plugin->blockContentBundle = 'custom_block';
    $return = $this->plugin->exclude();
    $this->assertFalse($return);
  }

  /**
   * Tests the includeOnly method.
   *
   * @see ::includeOnly()
   * @TODO Create a provider so that more combinations can be tested.
   */
  public function testIncludeOnly() {
    // stub the blockPlugin
    $blockPlugin = $this->prophesize(PluginInspectionInterface::CLASS);
    $blockPlugin->getPluginId()->willReturn('basic_block');
    $this->plugin->blockPlugin = $blockPlugin->reveal();

    // No include options are passed
    $return = $this->plugin->includeOnly();
    $this->assertTrue($return);

    // Include basic_block
    $this->plugin->pluginDefinition['include'] = ['basic_block'];
    $return = $this->plugin->includeOnly();
    $this->assertTrue($return);

    // Include only a sample_block
    $this->plugin->pluginDefinition['include'] = ['wrong_block'];
    $return = $this->plugin->includeOnly();
    $this->assertFalse($return);

    // Include a custom content block
    $this->plugin->pluginDefinition['include'] = ['custom_block'];
    $this->plugin->blockContentBundle = 'custom_block';
    $return = $this->plugin->includeOnly();
    $this->assertTrue($return);

    // Include a custom content block which is not the current one
    $this->plugin->pluginDefinition['include'] = ['wrong_custom_block'];
    $this->plugin->blockContentBundle = 'custom_block';
    $return = $this->plugin->includeOnly();
    $this->assertFalse($return);
  }

  /**
   * Tests the setBlockContentBundle method.
   *
   * @see ::setBlockContentBundle()
   */
  public function testSetBlockContentBundle() {
    // stub the blockPlugin
    $blockPlugin = $this->prophesize(DerivativeInspectionInterface::CLASS);
    $blockPlugin->getBaseId()->willReturn('block_content');
    $blockPlugin->getDerivativeId()->willReturn('uuid-1234');
    $this->plugin->blockPlugin = $blockPlugin->reveal();

    $entity = $this->prophesize(EntityInterface::CLASS);
    $entity->bundle()->willReturn('basic_custom_block');

    $this->entityRepository->loadEntityByUuid('block_content', 'uuid-1234')
      ->willReturn($entity->reveal());

    $this->plugin->setBlockContentBundle();
    $bundle = $this->plugin->blockContentBundle;

    $this->assertEquals('basic_custom_block', $bundle);
  }

}





































