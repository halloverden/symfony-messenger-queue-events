<?php


namespace HalloVerden\MessengerQueueEventsBundle\Command;

use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
use HalloVerden\MessengerQueueEventsBundle\Services\QueueEventServiceInterface;
use HalloVerden\MessengerQueueEventsBundle\Services\TransportNameServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class DispatchEventsCommand
 *
 * @package HalloVerden\MessengerQueueEventsBundle
 */
class DispatchEventsCommand extends Command {
  protected static $defaultName = 'hallo_verden:messenger_queue_events:dispatch';

  private EventDispatcherInterface $eventDispatcher;
  private QueueEventServiceInterface $queueEventService;
  private TransportNameServiceInterface $transportNameService;
  private array $transportEventInformationMapping;
  private bool $enabled;

  /**
   * DispatchEventsCommand constructor.
   *
   * @param EventDispatcherInterface      $eventDispatcher
   * @param QueueEventServiceInterface    $queueEventService
   * @param TransportNameServiceInterface $transportNameService
   * @param array                         $transportEventInformationMapping
   * @param bool                          $enabled
   */
  public function __construct(EventDispatcherInterface $eventDispatcher,
                              QueueEventServiceInterface $queueEventService,
                              TransportNameServiceInterface $transportNameService,
                              array $transportEventInformationMapping,
                              bool $enabled) {
    parent::__construct();
    $this->eventDispatcher = $eventDispatcher;
    $this->queueEventService = $queueEventService;
    $this->transportNameService = $transportNameService;
    $this->transportEventInformationMapping = $transportEventInformationMapping;
    $this->enabled = $enabled;
  }

  /**
   * @inheritDoc
   */
  protected function configure(): void {
    $this->setDescription('Dispatch message queue events')
      ->addArgument('transports', InputArgument::IS_ARRAY, 'transports you want to dispatch events for', []);
  }

  /**
   * @inheritDoc
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $io = new SymfonyStyle($input, $output);

    if (!$this->enabled) {
      $io->warning('queue events is not enabled');
      return Command::SUCCESS;
    }

    $dispatchedCount = 0;
    $transportNames = $this->transportNameService->getTransportNames();
    foreach ($transportNames as $transportName) {
      $eventInformationTypes = $this->transportEventInformationMapping[$transportName] ?? MessageQueueEvent::EVENT_INFORMATION_TYPES;

      if (empty($eventInformationTypes)) {
        if ($io->isVerbose()) {
          $io->note(\sprintf('Transport %s have no information types mapped, not sending a event.', $transportName));
        }

        continue;
      }

      if ($io->isVerbose()) {
        $io->note(\sprintf('Dispatching event for transport %s', $transportName));
      }

      $this->eventDispatcher->dispatch($this->createEvent($transportName, $eventInformationTypes));
      $dispatchedCount++;
    }

    $io->success(\sprintf('Dispatched %d events', $dispatchedCount));
    return Command::SUCCESS;
  }

  /**
   * @param string $transport
   * @param array  $eventInformationTypes
   *
   * @return MessageQueueEvent
   */
  private function createEvent(string $transport, array $eventInformationTypes): MessageQueueEvent {
    $event = new MessageQueueEvent($transport, $eventInformationTypes);
    $this->setInformation($event, $eventInformationTypes);

    return $event;
  }

  /**
   * @param MessageQueueEvent $event
   * @param array             $eventInformationTypes
   */
  private function setInformation(MessageQueueEvent $event, array $eventInformationTypes): void {
    foreach ($eventInformationTypes as $eventInformationType) {
      switch ($eventInformationType) {
        case MessageQueueEvent::EVENT_INFORMATION_MESSAGE_COUNT:
          $event->setMessageCount($this->queueEventService->getMessagesCount($event->getTransport()));
          break;
        case MessageQueueEvent::EVENT_INFORMATION_MESSAGES_NOT_DELAYED_COUNT:
          $event->setMessagesNotDelayed($this->queueEventService->getMessagesNotDelayedCount($event->getTransport()));
          break;
        case MessageQueueEvent::EVENT_INFORMATION_LAST_AVAILABLE_MESSAGE:
          $event->setLastAvailableMessage($this->queueEventService->getLastAvailableMessage($event->getTransport()));
          break;
        case MessageQueueEvent::EVENT_INFORMATION_FIRST_AVAILABLE_MESSAGE:
          $event->setFirstAvailableMessage($this->queueEventService->getFirstAvailable($event->getTransport()));
          break;
      }
    }
  }

}
