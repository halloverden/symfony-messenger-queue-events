<?php


namespace HalloVerden\MessengerQueueEventsBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use HalloVerden\MessengerQueueEventsBundle\Messenger\Stamp\QueueEventMessageUuidStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Uid\Uuid;

/**
 * Class QueueEventMessage
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Entity
 *
 * @ORM\Entity()
 */
class QueueEventMessage {

  /**
   * @var int
   *
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(name="id", type="integer", unique=true)
   */
  private int $id;

  /**
   * This is a string because I don't want to collide with someone using ramsey\uuid, since the doctrine type for this have the same name as symfony/uid :(
   *   This is NOT unique, since the same message can be added multiple times with different transports.
   *
   * @var string
   *
   * @ORM\Column(name="uuid", type="string", unique=false)
   */
  private string $uuid;

  /**
   * @var string|null
   *
   * @ORM\Column(name="transport", type="string", nullable=true)
   */
  private ?string $transport;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   */
  private \DateTime $createdAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="available_at", type="datetime", nullable=false)
   */
  private \DateTime $availableAt;

  /**
   * QueueEventMessage constructor.
   *
   */
  public function __construct() {
    $this->uuid = Uuid::v4();
    $this->availableAt = new \DateTime();
    $this->createdAt = new \DateTime();
  }

  /**
   * @param Envelope $envelope
   * @param string   $transport
   *
   * @return static
   * @throws \Exception
   */
  public static function createFromEnvelope(Envelope $envelope, string $transport): self {
    /** @var QueueEventMessageUuidStamp $uuidStamp */
    $uuidStamp = $envelope->last(QueueEventMessageUuidStamp::class);

    $queueEventMessage = new static();
    $queueEventMessage->uuid = $uuidStamp->getUuid();
    $queueEventMessage->transport = $transport;

    /** @var DelayStamp $delay */
    if ($delay = $envelope->last(DelayStamp::class)) {
      $queueEventMessage->availableAt = new \DateTime('@' . (time() + ($delay->getDelay() / 1000)));
    }

    return $queueEventMessage;
  }

  /**
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * @return Uuid
   */
  public function getUuid(): Uuid {
    return Uuid::fromString($this->uuid);
  }

  /**
   * @return string|null
   */
  public function getTransport(): ?string {
    return $this->transport;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt(): \DateTime {
    return $this->createdAt;
  }

  /**
   * @return \DateTime
   */
  public function getAvailableAt(): \DateTime {
    return $this->availableAt;
  }

}
