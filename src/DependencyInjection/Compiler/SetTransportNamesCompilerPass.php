<?php


namespace HalloVerden\MessengerQueueEventsBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SetTransportsCompilerPass
 *
 * @package HalloVerden\MessengerQueueEventsBundle\DependencyInjection\Compiler
 */
class SetTransportNamesCompilerPass implements CompilerPassInterface {

  /**
   * @inheritDoc
   * @throws \Exception
   */
  public function process(ContainerBuilder $container): void {
    $receivers = $container->findTaggedServiceIds('messenger.receiver');
    $transports = [];

    // If there is a better way to get all transport names, I would like to know.
    foreach ($receivers as $receiverTags) {
      foreach ($receiverTags as $receiverTag) {
        if (isset($receiverTag['alias'])) {
          $transports[] = $receiverTag['alias'];
        }
      }
    }

    $mapping = $container->getParameter('hallo_verden_messenger_queue_events.transport_event_information_mapping');
    foreach (\array_keys($mapping) as $transport) {
      if (!\in_array($transport, $transports)) {
        throw new \Exception(\sprintf('%s is not a valid transport, check transport_event_information_mapping config', $transport));
      }
    }

    if (empty($transports)) {
      return;
    }

    $dispatchEventCommandDefinition = $container->getDefinition('hallo_verden_queue_events.transport_name_service');
    $dispatchEventCommandDefinition->setArgument('$transportNames', $transports);
  }

}
