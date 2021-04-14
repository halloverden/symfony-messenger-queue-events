<?php


namespace HalloVerden\MessengerQueueEventsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package HalloVerden\MessengerQueueEventsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface {

  /**
   * @inheritDoc
   */
  public function getConfigTreeBuilder(): TreeBuilder {
    $treeBuilder = new TreeBuilder('hallo_verden_messenger_queue_events');

    // TODO: define config tree

    return $treeBuilder;
  }

}
