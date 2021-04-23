<?php


namespace HalloVerden\MessengerQueueEventsBundle\Services;

/**
 * Interface TransportNameServiceInterface
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Services
 */
interface TransportNameServiceInterface {

  /**
   * @return string[]
   */
  public function getTransportNames(): array;

}
