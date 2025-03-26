<?php

namespace Wiva\EventBridgeMessenger;

use Aws\EventBridge\EventBridgeClient;
use Aws\Exception\AwsException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class EventBridgeTransport implements TransportInterface
{

    public function __construct(
        private EventBridgeClient $client,
        private SerializerInterface $serializer,
        private string $eventBusName,
        private string $source
    ) {}

    public function send(Envelope $envelope): Envelope
    {
        $encodedMessage = $this->serializer->encode($envelope);
        $detailType = $encodedMessage['eventName'] ?? 'UnknownType';
        $detail = $encodedMessage['detail'];


        try {
            $result = $this->client->putEvents([
                'Entries' => [
                    [
                        'Source' => $this->source,
                        'DetailType' => $detailType,
                        'Detail' => $detail,
                        'EventBusName' => $this->eventBusName,
                    ],
                ],
            ]);
        } catch (AwsException $e) {
            throw new TransportException('Could not send message to EventBridge: '.$e->getMessage(), 0, $e);
        }

        return $envelope;
    }

    public function receive(callable $handler): void
    {
        // EventBridge is primarily an event bus and does not directly support receiving messages in the context of Symfony Messenger.
        // You might need to implement a custom solution to pull events from EventBridge, if necessary.
        throw new \LogicException('EventBridgeTransport does not support receiving messages.');
    }

    public function get(): iterable
    {
        // EventBridge is primarily an event bus and does not directly support receiving messages in the context of Symfony Messenger.
    }

    public function ack(Envelope $envelope): void
    {
        // EventBridge is primarily an event bus and does not directly support receiving messages in the context of Symfony Messenger.
    }

    public function reject(Envelope $envelope): void
    {
        // EventBridge is primarily an event bus and does not directly support receiving messages in the context of Symfony Messenger.
    }
}