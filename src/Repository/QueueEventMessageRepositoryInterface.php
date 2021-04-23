<?php


namespace HalloVerden\MessengerQueueEventsBundle\Repository;

use HalloVerden\MessengerQueueEventsBundle\Entity\QueueEventMessage;
use Symfony\Component\Uid\Uuid;

/**
 * Interface QueueEventMessageRepositoryInterface
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Repository
 */
interface QueueEventMessageRepositoryInterface {

  /**
   * @param QueueEventMessage $queueEventMessage
   *
   * @return QueueEventMessage
   */
  public function create(QueueEventMessage $queueEventMessage): QueueEventMessage;

  /**
   * @param Uuid   $uuid
   * @param string $transport
   */
  public function deleteByUuidAndTransport(Uuid $uuid, string $transport): void;

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
   * @return QueueEventMessage|null
   */
  public function getFirstAvailableMessage(string $transport): ?QueueEventMessage;

  /**
   * @param string $transport
   *
   * @return QueueEventMessage
   */
  public function getLastAvailableMessage(string $transport): ?QueueEventMessage;

}
