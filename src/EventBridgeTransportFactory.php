<?php

namespace Wiva\EventBridgeMessenger;

use Aws\EventBridge\EventBridgeClient;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class EventBridgeTransportFactory implements TransportFactoryInterface
{
    public function __construct(private EventBridgeSerializer $serializer) {}

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $configuration = $this->getConfiguration($dsn, $options);

        $client = new EventBridgeClient([
            'region' => $configuration['region'],
            'version' => 'latest',
            'credentials' => [
                'key' => $configuration['key'],
                'secret' => $configuration['secret'],
            ],
            'endpoint' => $configuration['endpoint'],
            'debug' => $configuration['debug'],
        ]);

        $serializer = $this->serializer;

        return new EventBridgeTransport($client, $serializer, $configuration['eventBusName'], $configuration['source']);
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'eventbridge://');
    }

    private function getConfiguration(string $dsn, array $options): array
    {
        $parsedUrl = parse_url($dsn);
        if (false === $parsedUrl) {
            throw new InvalidArgumentException(sprintf('The given DSN "%s" is invalid.', $dsn));
        }

        return [
            'region' => $options['region'] ?? 'us-east-1',
            'key' => $options['key'] ?? '',
            'secret' => $options['secret'] ?? '',
            'endpoint' => $options['endpoint'] ?? '',
            'eventBusName' => $options['eventBusName'] ?? 'default',
            'source' => $options['source'] ?? 'symfony',
            'debug' => $options['debug'] ?? false,
        ];
    }
}