<?php

namespace Drupal\Tests\block_style_plugins\Unit\Plugin\BlockStyle;

use Drupal\Tests\UnitTestCase;
use Drupal\block_style_plugins\Plugin\BlockStyle\SampleBlockStyle;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * @coversDefaultClass \Drupal\block_style_plugins\Plugin\BlockStyle\SampleBlockStyle
 * @group block_style_plugins
 */
class SampleBlockStyleTest extends UnitTestCase
{

  /**
   * @var \Drupal\block_style_plugins\Plugin\BlockStyle\SampleBlockStyle
   */
  protected $plugin;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp()
  {
    parent::setUp();

    // Stub the Iconset Finder Service
    $entityRepository = $this->prophesize(EntityRepositoryInterface::CLASS);

    // Stub the Entity Type Manager
    $entityTypeManager = $this->prophesize(EntityTypeManagerInterface::CLASS);

    $configuration = [];
    $plugin_id = 'block_style_plugins';
    $plugin_definition['provider'] = 'block_style_plugins';

    $this->plugin = new SampleBlockStyle(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entityRepository->reveal(),
      $entityTypeManager->reveal()
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Tests the defaultStyles method.
   *
   * @see ::defaultStyles()
   */
  public function testDefaultStyles() {
    $expected = ['sample_class' => ''];
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
    $formState = $this->prophesize(FormStateInterface::CLASS);

    $default = $this->plugin->formElements($form, $formState->reveal());

    $this->assertArrayHasKey('sample_class', $default);
    $this->assertArrayHasKey('#default_value', $default['sample_class']);
  }

}
