Configuration
=============

```yaml
hallo_verden_messenger_queue_events:
    enabled: true
    transport_event_information_mapping:
        sync: []
        async: ~
        failed:
            - message_count
```

Here no event is dispatched for sync transport. 
All information is added to async transport, and message_count is added to event for failed transport.

available event information types:
- `message_count` - Number of messages currently in transport.
- `messages_not_delayed_count` - Number of messages that is not delayed in transport. 
- `first_available_message` - The first available message in transport (The first in queue).
- `last_available_message` - The last available message in transport (The last in queue).

Usage
=====

Create a cronjob that executes `bin/console hallo_verden:messenger_queue_events:dispatch`
at a desired interval (i.e. every 15 seconds)

Now you can create a event listener and act on the `MessageQueueEvent`:

```php
use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class MessageQueueListner implements EventSubscriberInterface {

     
  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return [
      MessageQueueEvent::class => 'onMessageQueueEvent'
    ];
  }
  
  public function onMessageQueueEvent(MessageQueueEvent $event): void {
    // TODO do something with the event
  }

}
```
