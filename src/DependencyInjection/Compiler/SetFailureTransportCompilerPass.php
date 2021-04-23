<?php


namespace HalloVerden\MessengerQueueEventsBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SetFailureTransportCompilerPass
 *
 * @package HalloVerden\MessengerQueueEventsBundle\DependencyInjection\Compiler
 */
class SetFailureTransportCompilerPass implements CompilerPassInterface {

  /**
   * @inheritDoc
   */
  public function process(ContainerBuilder $container): void {
    if (!$container->hasDefinition('console.command.messenger_failed_messages_retry')) {
      $container->removeDefinition('hallo_verden_queue_events.listener.store_queue_event_message_on_failed');
      return;
    }

    $retryCommandDefinition = $container->getDefinition('console.command.messenger_failed_messages_retry');
    $failureTransport =  $retryCommandDefinition->getArgument(0);

    $container->getDefinition('hallo_verden_queue_events.listener.store_queue_event_message_on_failed')
      ->setArgument('$failureTransport', $failureTransport);

    $container->getDefinition('hallo_verden_queue_events.middleware.remove_message')
      ->setArgument('$failureTransport', $failureTransport);
  }

}
