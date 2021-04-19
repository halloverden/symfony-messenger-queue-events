<?php


namespace HalloVerden\MessengerQueueEventsBundle\Services;

use HalloVerden\MessengerQueueEventsBundle\Entity\QueueEventMessage;

/**
 * Interface QueueEventServiceInterface
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Services
 */
interface QueueEventServiceInterface {

  /**
   * @param string $transport
   *
   * @return int
   */
  public function getMessagesCount(string $transport): int;

  /**
   * @param string $transport
   *
   * @return int
   */
  public function getMessagesNotDelayedCount(string $transport): int;

  /**
   * @param string $transport
   *
   * @return QueueEventMessage
   */
  public function getFirstAvailable(string $transport): ?QueueEventMessage;

  /**
   * @param string $transport
   *
   * @return QueueEventMessage
   */
  public function getLastAvailableMessage(string $transport): ?QueueEventMessage;

}
