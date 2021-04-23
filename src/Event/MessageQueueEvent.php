<?php


namespace HalloVerden\MessengerQueueEventsBundle\Event;


use HalloVerden\MessengerQueueEventsBundle\Entity\QueueEventMessage;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class MessageQueueEvent
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Event
 */
class MessageQueueEvent extends Event {
  const EVENT_INFORMATION_MESSAGE_COUNT = 'message_count';
  const EVENT_INFORMATION_MESSAGES_NOT_DELAYED_COUNT = 'messages_not_delayed_count';
  const EVENT_INFORMATION_FIRST_AVAILABLE_MESSAGE = 'first_available_message';
  const EVENT_INFORMATION_LAST_AVAILABLE_MESSAGE = 'last_available_message';

  const EVENT_INFORMATION_TYPES = [
    self::EVENT_INFORMATION_MESSAGE_COUNT,
    self::EVENT_INFORMATION_MESSAGES_NOT_DELAYED_COUNT,
    self::EVENT_INFORMATION_FIRST_AVAILABLE_MESSAGE,
    self::EVENT_INFORMATION_LAST_AVAILABLE_MESSAGE,
  ];

  private string $transport;
  private ?int $messageCount = null;
  private ?int $messagesNotDelayed = null;
  private ?QueueEventMessage $firstAvailableMessage = null;
  private ?QueueEventMessage $lastAvailableMessage = null;

  /**
   * @var string[]
   */
  private array $eventInformationTypes;

  /**
   * MessageQueueEvent constructor.
   *
   * @param string $transport
   * @param array  $eventInformationTypes
   */
  public function __construct(string $transport, array $eventInformationTypes) {
    $this->transport = $transport;
    $this->eventInformationTypes = $eventInformationTypes;
  }

  /**
   * @return string
   */
  public function getTransport(): string {
    return $this->transport;
  }

  /**
   * @return int|null
   */
  public function getMessageCount(): ?int {
    $this->checkEventInformationType(self::EVENT_INFORMATION_MESSAGE_COUNT);

    return $this->messageCount;
  }

  /**
   * @param int|null $messageCount
   *
   * @return MessageQueueEvent
   */
  public function setMessageCount(?int $messageCount): self {
    $this->checkEventInformationType(self::EVENT_INFORMATION_MESSAGE_COUNT);

    $this->messageCount = $messageCount;
    return $this;
  }

  /**
   * @return int|null
   */
  public function getMessagesNotDelayed(): ?int {
    $this->checkEventInformationType(self::EVENT_INFORMATION_MESSAGES_NOT_DELAYED_COUNT);

    return $this->messagesNotDelayed;
  }

  /**
   * @param int|null $messagesNotDelayed
   *
   * @return MessageQueueEvent
   */
  public function setMessagesNotDelayed(?int $messagesNotDelayed): self {
    $this->checkEventInformationType(self::EVENT_INFORMATION_MESSAGES_NOT_DELAYED_COUNT);

    $this->messagesNotDelayed = $messagesNotDelayed;
    return $this;
  }

  /**
   * @return QueueEventMessage|null
   */
  public function getFirstAvailableMessage(): ?QueueEventMessage {
    $this->checkEventInformationType(self::EVENT_INFORMATION_FIRST_AVAILABLE_MESSAGE);

    return $this->firstAvailableMessage;
  }

  /**
   * @param QueueEventMessage|null $firstAvailableMessage
   *
   * @return MessageQueueEvent
   */
  public function setFirstAvailableMessage(?QueueEventMessage $firstAvailableMessage): self {
    $this->checkEventInformationType(self::EVENT_INFORMATION_FIRST_AVAILABLE_MESSAGE);

    $this->firstAvailableMessage = $firstAvailableMessage;
    return $this;
  }

  /**
   * @return QueueEventMessage|null
   */
  public function getLastAvailableMessage(): ?QueueEventMessage {
    $this->checkEventInformationType(self::EVENT_INFORMATION_LAST_AVAILABLE_MESSAGE);

    return $this->lastAvailableMessage;
  }

  /**
   * @param QueueEventMessage|null $lastAvailableMessage
   *
   * @return MessageQueueEvent
   */
  public function setLastAvailableMessage(?QueueEventMessage $lastAvailableMessage): self {
    $this->checkEventInformationType(self::EVENT_INFORMATION_LAST_AVAILABLE_MESSAGE);
    $this->lastAvailableMessage = $lastAvailableMessage;
    return $this;
  }

  /**
   * @param string $eventInformation
   */
  private function checkEventInformationType(string $eventInformation): void {
    if (!\in_array($eventInformation, $this->eventInformationTypes)) {
      throw new \LogicException(\sprintf('"%s" is not in your subscribed event information types, check your event information mapping.', $eventInformation));
    }
  }

}
