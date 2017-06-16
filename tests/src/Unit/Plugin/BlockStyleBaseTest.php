<?php

namespace Drupal\Tests\block_style_plugins\Unit\Plugin;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;

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
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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

    // Stub the Iconset Finder Service
    $this->entityRepository = $this->prophesize(EntityRepositoryInterface::CLASS);

    // Stub the Entity Type Manager
    $this->entityTypeManager = $this->prophesize(EntityTypeManagerInterface::CLASS);

    // Form state double
    $this->formState = $this->prophesize(FormStateInterface::CLASS);

    $configuration = [];
    $plugin_id = 'block_style_plugins';
    $plugin_definition['provider'] = 'block_style_plugins';

    $this->plugin = new MockBlockStyleBase(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $this->entityRepository->reveal(),
      $this->entityTypeManager->reveal()
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
    $container->get('entity_type.manager')->willReturn($this->entityTypeManager->reveal());

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
   * @see ::defaultStyles()
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
   * Tests the build method.
   *
   * @see ::build()
   * @TODO Create a provider so that more combinations can be tested.
   */
  public function testBuild() {
    $block = $this->prophesize(ConfigEntityInterface::CLASS);

    $storage = $this->prophesize(EntityStorageInterface::CLASS);
    $storage->load(1)->willReturn($block->reveal());

    $this->entityTypeManager->getStorage('block')->willReturn($storage->reveal());

    // No element ID is passed through the variables.
    $variables = [];
    $return = $this->plugin->build($variables);
    $this->assertArrayEquals($variables, $return);

    // No styles attached to the block.
    $block->getThirdPartySetting('block_style_plugins', 'block_style_plugins')
      ->willReturn(FALSE);

    $variables = ['elements' => ['#id' => 1]];
    $return = $this->plugin->build($variables);
    $this->assertArrayEquals($variables, $return);

    // Return the third party styles set in the plugin.
    $block->getThirdPartySetting('block_style_plugins', 'block_style_plugins')
      ->willReturn(['class1', 'class2']);

    $variables = ['elements' => ['#id' => 1]];
    $expected = [
      'elements' => ['#id' => 1],
      'block_styles' => [
        'block_style_plugins' => ['class1', 'class2']
      ],
      'attributes' => [
        'class' => [
          'class1',
          'class2'
        ]
      ]
    ];
    $return = $this->plugin->build($variables);
    $this->assertArrayEquals($expected, $return);

    // Don't set a class for integers.
    $block->getThirdPartySetting('block_style_plugins', 'block_style_plugins')
      ->willReturn(['class1', 1, 'class2', 0]);

    $variables = ['elements' => ['#id' => 1]];
    $expected = [
      'elements' => ['#id' => 1],
      'block_styles' => [
        'block_style_plugins' => ['class1', 1, 'class2', 0]
      ],
      'attributes' => [
        'class' => [
          'class1',
          'class2'
        ]
      ]
    ];
    $return = $this->plugin->build($variables);
    $this->assertArrayEquals($expected, $return);
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
   *
   * @dataProvider excludeProvider
   */
  public function testExclude($plugin, $bundle, $expected) {
    // stub the blockPlugin
    $blockPlugin = $this->prophesize(PluginInspectionInterface::CLASS);
    $blockPlugin->getPluginId()->willReturn('basic_block');
    $this->plugin->blockPlugin = $blockPlugin->reveal();

    if ($plugin) {
      $this->plugin->pluginDefinition['exclude'] = [$plugin];
    }
    if ($bundle) {
      $this->plugin->blockContentBundle = $bundle;
    }
    $return = $this->plugin->exclude();
    $this->assertEquals($expected, $return);
  }

  /**
   * Provider for testExclude()
   */
  public function excludeProvider() {
    return [
      'No exclude options are passed' => [FALSE, NULL, FALSE],
      'Exclude basic_block' => ['basic_block', NULL, TRUE],
      'Exclude a block that is not the current one' => ['wrong_block', NULL, FALSE],
      'Exclude a custom content block' => ['custom_block', 'custom_block', TRUE],
      'Exclude a custom content block that is not the current block' => ['wrong_custom_block', 'custom_block', FALSE],
    ];
  }

  /**
   * Tests the includeOnly method.
   *
   * @see ::includeOnly()
   *
   * @dataProvider includeOnlyProvider
   */
  public function testIncludeOnly($plugin, $bundle, $expected) {
    // stub the blockPlugin
    $blockPlugin = $this->prophesize(PluginInspectionInterface::CLASS);
    $blockPlugin->getPluginId()->willReturn('basic_block');
    $this->plugin->blockPlugin = $blockPlugin->reveal();

    if ($plugin) {
      $this->plugin->pluginDefinition['include'] = [$plugin];
    }
    if ($bundle) {
      $this->plugin->blockContentBundle = $bundle;
    }
    $return = $this->plugin->includeOnly();
    $this->assertEquals($expected, $return);
  }

  /**
   * Provider for testIncludeOnly()
   */
  public function includeOnlyProvider() {
    return [
      'No include options are passed' => [NULL, NULL, TRUE],
      'Include basic_block' => ['basic_block', NULL, TRUE],
      'Include only a sample_block' => ['wrong_block', NULL, FALSE],
      'Include a custom content block' => ['custom_block', 'custom_block', TRUE],
      'Include a custom content block which is not the current one' => ['wrong_custom_block', 'custom_block', FALSE],
    ];
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
