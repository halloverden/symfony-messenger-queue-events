<?php


namespace HalloVerden\MessengerQueueEventsBundle\DependencyInjection;


use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
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

    $treeBuilder->getRootNode()
      ->children()
        ->booleanNode('enabled')->defaultFalse()->end()
        ->arrayNode('transport_event_information_mapping')
          ->variablePrototype()
            ->validate()
              ->ifTrue(fn($v) => null !== $v && !\is_array($v))
              ->thenInvalid('Must be array or null')
              ->ifTrue(function ($v) {
                foreach ($v ?? [] as $eventInformation) {
                  if (!in_array($eventInformation, MessageQueueEvent::EVENT_INFORMATION_TYPES)) {
                    return true;
                  }
                }
                return false;
              })
              ->thenInvalid(\sprintf('Must be one of %s', implode(', ', MessageQueueEvent::EVENT_INFORMATION_TYPES)))
            ->end()
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }

}
