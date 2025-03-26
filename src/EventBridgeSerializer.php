<?php

declare(strict_types=1);

namespace Wiva\EventBridgeMessenger;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializationException;
use Symfony\Component\Messenger\Envelope;

class EventBridgeSerializer implements SerializerInterface
{
    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if (!method_exists($message, 'eventName')) {
            throw new SerializationException('Message must have a eventName() method');
        }

        if (!method_exists($message, 'toPayload')) {
            throw new SerializationException('Message must have a toPayload() method');
        }

        if (!method_exists($message, 'toHeaders')) {
            throw new SerializationException('Message must have a toHeaders() method');
        }

        $eventName = $message->eventName();

        $detail = json_encode([
            'metadata' => $message->toHeaders(),
            'data' => $message->toPayload(),
        ]);


        if ($detail === false) {
            throw new SerializationException('Cannot serialize event payload');
        }

        return [
            'eventName' => $eventName,
            'detail' => $detail,
        ];
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        throw new SerializationException('Receiving messages from EventBridge is not supported');
    } 
} 