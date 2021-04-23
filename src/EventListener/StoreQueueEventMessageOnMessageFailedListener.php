<?php


namespace HalloVerden\MessengerQueueEventsBundle\EventListener;


use HalloVerden\MessengerQueueEventsBundle\Entity\QueueEventMessage;
use HalloVerden\MessengerQueueEventsBundle\Messenger\Stamp\QueueEventMessageUuidStamp;
use HalloVerden\MessengerQueueEventsBundle\Repository\QueueEventMessageRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\DelayStamp;

/**
 * Class StoreQueueEventMessageOnMessageFailedListener
 *
 * @package HalloVerden\MessengerQueueEventsBundle\EventListener
 */
class StoreQueueEventMessageOnMessageFailedListener implements EventSubscriberInterface {
  private QueueEventMessageRepositoryInterface $queueEventMessageRepository;
  private string $failureTransport;

  /**
   * StoreQueueEventMessageOnMessageFailedListener constructor.
   *
   * @param QueueEventMessageRepositoryInterface $queueEventMessageRepository
   * @param string                               $failureTransport
   */
  public function __construct(QueueEventMessageRepositoryInterface $queueEventMessageRepository, string $failureTransport) {
    $this->queueEventMessageRepository = $queueEventMessageRepository;
    $this->failureTransport = $failureTransport;
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return [
      WorkerMessageFailedEvent::class => 'onMessageFailed'
    ];
  }

  /**
   * @param WorkerMessageFailedEvent $event
   *
   * @throws \Exception
   */
  public function onMessageFailed(WorkerMessageFailedEvent $event): void {
    if ($event->willRetry()) {
      return;
    }

    /** @var QueueEventMessageUuidStamp $uuidStamp */
    $uuidStamp = $event->getEnvelope()->last(QueueEventMessageUuidStamp::class);

    if (!$uuidStamp) {
      return;
    }

    $this->queueEventMessageRepository->deleteByUuidAndTransport($uuidStamp->getUuid(), $event->getReceiverName());

    // We just assume the message was or is going to be sent to failure transport, since there is no way to actually know.
    $this->queueEventMessageRepository->create(
      QueueEventMessage::createFromEnvelope($event->getEnvelope()->with(new DelayStamp(0)), $this->failureTransport)
    );
  }

}
