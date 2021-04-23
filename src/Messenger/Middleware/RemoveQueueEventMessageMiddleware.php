<?php


namespace HalloVerden\MessengerQueueEventsBundle\Messenger\Middleware;

use HalloVerden\MessengerQueueEventsBundle\Messenger\Stamp\QueueEventMessageUuidStamp;
use HalloVerden\MessengerQueueEventsBundle\Repository\QueueEventMessageRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;

/**
 * Class RemoveQueueEventMessageMiddleware
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Messenger\Middleware
 */
class RemoveQueueEventMessageMiddleware implements MiddlewareInterface {
  private QueueEventMessageRepositoryInterface $queueEventMessageRepository;
  private bool $enabled;
  private ?string $failureTransport;

  /**
   * RemoveQueueEventMessageMiddleware constructor.
   */
  public function __construct(QueueEventMessageRepositoryInterface $queueEventMessageRepository, bool $enabled, ?string $failureTransport = null) {
    $this->queueEventMessageRepository = $queueEventMessageRepository;
    $this->enabled = $enabled;
    $this->failureTransport = $failureTransport;
  }

  /**
   * @param Envelope       $envelope
   * @param StackInterface $stack
   *
   * @return Envelope
   */
  public function handle(Envelope $envelope, StackInterface $stack): Envelope {
    $envelope = $stack->next()->handle($envelope, $stack);

    if (!$this->enabled || !$envelope->last(HandledStamp::class)) {
      return $envelope;
    }

    /** @var QueueEventMessageUuidStamp $uuidStamp */
    $uuidStamp = $envelope->last(QueueEventMessageUuidStamp::class);
    if ($uuidStamp && ($transport = $this->getTransport($envelope))) {
      $this->queueEventMessageRepository->deleteByUuidAndTransport($uuidStamp->getUuid(), $transport);
    }

    return $envelope;
  }

  /**
   * @param Envelope $envelope
   *
   * @return string|null
   */
  private function getTransport(Envelope $envelope): ?string {
    // When processing a failed message, FailedMessageProcessingMiddleware sets ReceivedStamp to the originalReceiverName,
    //   so we can't get the transport name from that for failed messages.
    if (null !== $envelope->last(SentToFailureTransportStamp::class) && null !== $this->failureTransport) {
      return $this->failureTransport;
    }

    /** @var ReceivedStamp $receivedStamp */
    $receivedStamp = $envelope->last(ReceivedStamp::class);
    if (null !== $receivedStamp) {
      return $receivedStamp->getTransportName();
    }

    return null;
  }

}
