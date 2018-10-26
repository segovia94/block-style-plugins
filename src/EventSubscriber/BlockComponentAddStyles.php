<?php

namespace Drupal\block_style_plugins\EventSubscriber;

use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds styles for all block components.
 */
class BlockComponentAddStyles implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = 'afterBuildRender';
    return $events;
  }

  /**
   * Appends styles to the build render array and sets it on the event.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   The section component render event.
   */
  public function afterBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    $component = $event->getComponent();
    $block_styles = $component->get('third_party_settings');

    if ($block_styles) {
      $build = $event->getBuild();
      // Add styles to the configuration array so that they can be accessed in a
      // block preprocess $variables.
      $build['#configuration']['block_style_plugins'] = $block_styles['block_style_plugins'];
      $event->setBuild($build);
    }
  }

}
