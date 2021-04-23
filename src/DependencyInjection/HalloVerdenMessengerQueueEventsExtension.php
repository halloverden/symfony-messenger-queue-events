<?php


namespace HalloVerden\MessengerQueueEventsBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class HalloVerdenMessengerQueueEventsExtension
 *
 * @package HalloVerden\MessengerQueueEventsBundle\DependencyInjection
 */
class HalloVerdenMessengerQueueEventsExtension extends ConfigurableExtension {

  /**
   * @inheritDoc
   * @throws \Exception
   */
  protected function loadInternal(array $mergedConfig, ContainerBuilder $container) {
    $loader = new YamlFileLoader($container, new FileLocator(__DIR__. '/../../config'));
    $loader->load('services.yaml');

    $container->setParameter('hallo_verden_messenger_queue_events.enabled', $mergedConfig['enabled']);

    $dispatchEventsCommandDefinition = $container->getDefinition('hallo_verden_queue_events.dispatch_events_command');
    $dispatchEventsCommandDefinition->setArgument('$transportEventInformationMapping', $mergedConfig['transport_event_information_mapping']);

    $container->setParameter('hallo_verden_messenger_queue_events.transport_event_information_mapping', $mergedConfig['transport_event_information_mapping']);
  }

}
