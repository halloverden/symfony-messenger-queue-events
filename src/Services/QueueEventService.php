<?php


namespace HalloVerden\MessengerQueueEventsBundle\Services;

use HalloVerden\MessengerQueueEventsBundle\Entity\QueueEventMessage;
use HalloVerden\MessengerQueueEventsBundle\Repository\QueueEventMessageRepositoryInterface;

/**
 * Class QueueEventService
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Services
 */
class QueueEventService implements QueueEventServiceInterface {
  private QueueEventMessageRepositoryInterface $queueEventMessageRepository;

  /**
   * QueueEventService constructor.
   */
  public function __construct(QueueEventMessageRepositoryInterface $queueEventMessageRepository) {
    $this->queueEventMessageRepository = $queueEventMessageRepository;
  }

  /**
   * @inheritDoc
   */
  public function getMessagesCount(string $transport): int {
    return $this->queueEventMessageRepository->getMessagesCount($transport);
  }

  /**
   * @inheritDoc
   */
  public function getMessagesNotDelayedCount(string $transport): int {
    return $this->queueEventMessageRepository->getMessagesNotDelayedCount($transport);
  }

  /**
   * @inheritDoc
   */
  public function getFirstAvailable(string $transport): ?QueueEventMessage {
    return $this->queueEventMessageRepository->getFirstAvailableMessage($transport);
  }

  /**
   * @inheritDoc
   */
  public function getLastAvailableMessage(string $transport): ?QueueEventMessage {
    return $this->queueEventMessageRepository->getLastAvailableMessage($transport);
  }

}
