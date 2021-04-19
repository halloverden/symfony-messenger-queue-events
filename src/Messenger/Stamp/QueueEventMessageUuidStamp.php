<?php


namespace HalloVerden\MessengerQueueEventsBundle\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Class QueueEventMessageUuidStamp
 *
 * @package HalloVerden\MessengerQueueEventsBundle\Messenger\Stamp
 */
class QueueEventMessageUuidStamp implements StampInterface {
  private Uuid $uuid;

  /**
   * QueueEventMessageUuidStamp constructor.
   */
  public function __construct() {
    $this->uuid = Uuid::v4();
  }

  /**
   * @return Uuid
   */
  public function getUuid(): Uuid {
    return $this->uuid;
  }

}
