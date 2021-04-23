<?php


namespace HalloVerden\MessengerQueueEventsBundle\Messenger\Middleware;


use HalloVerden\MessengerQueueEventsBundle\Entity\QueueEventMessage;
use HalloVerden\MessengerQueueEventsBundle\Messenger\Stamp\QueueEventMessageUuidStamp;
use HalloVerden\MessengerQueueEventsBundle\Repository\QueueEventMessageRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

/**
 * Class StoreQueueEventMessageMiddleware
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Messenger\Middleware
 */
class StoreQueueEventMessageMiddleware implements MiddlewareInterface {
  private QueueEventMessageRepositoryInterface $queueEventMessageRepository;
  private SendersLocatorInterface $sendersLocator;
  private bool $enabled;

  /**
   * StoreQueueEventMessageMiddleware constructor.
   *
   * @param QueueEventMessageRepositoryInterface $queueEventMessageRepository
   * @param SendersLocatorInterface              $sendersLocator
   * @param bool                                 $enabled
   */
  public function __construct(QueueEventMessageRepositoryInterface $queueEventMessageRepository, SendersLocatorInterface $sendersLocator, bool $enabled) {
    $this->queueEventMessageRepository = $queueEventMessageRepository;
    $this->sendersLocator = $sendersLocator;
    $this->enabled = $enabled;
  }

  /**
   * @param Envelope       $envelope
   * @param StackInterface $stack
   *
   * @return Envelope
   * @throws \Exception
   */
  public function handle(Envelope $envelope, StackInterface $stack): Envelope {
    if (!$this->enabled || $envelope->last(ReceivedStamp::class)) {
      return $stack->next()->handle($envelope, $stack);
    }

    $envelope = $envelope->with(new QueueEventMessageUuidStamp());

    foreach ($this->sendersLocator->getSenders($envelope) as $alias => $sender) {
      $this->queueEventMessageRepository->create(QueueEventMessage::createFromEnvelope($envelope, $alias));
    }

    return $stack->next()->handle($envelope, $stack);
  }

}
