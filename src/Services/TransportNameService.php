<?php


namespace HalloVerden\MessengerQueueEventsBundle\Services;

/**
 * Class TransportNameService
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Services
 */
class TransportNameService implements TransportNameServiceInterface {

  /**
   * @var string[]
   */
  private array $transportNames;

  /**
   * TransportNameService constructor.
   *
   * @param string[] $transportNames
   */
  public function __construct(array $transportNames = []) {
    $this->transportNames = $transportNames;
  }

  /**
   * @inheritDoc
   */
  public function getTransportNames(): array {
    return $this->transportNames;
  }

}
