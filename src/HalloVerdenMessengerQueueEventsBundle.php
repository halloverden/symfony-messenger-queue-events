<?php


namespace HalloVerden\MessengerQueueEventsBundle;


use HalloVerden\MessengerQueueEventsBundle\DependencyInjection\Compiler\SetFailureTransportCompilerPass;
use HalloVerden\MessengerQueueEventsBundle\DependencyInjection\Compiler\SetTransportNamesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class HalloVerdenMessengerQueueEventsBundle
 *
 * @package HalloVerden\MessengerQueueEventsBundle
 */
class HalloVerdenMessengerQueueEventsBundle extends Bundle {

  /**
   * @inheritDoc
   */
  public function build(ContainerBuilder $container) {
    parent::build($container);

    $container
      ->addCompilerPass(new SetTransportNamesCompilerPass())
      ->addCompilerPass(new SetFailureTransportCompilerPass());
  }


}
