<?php

namespace Wiva\EventBridgeMessenger;

interface EventBridgeDomainEvent
{
    public function toHeaders(): ?array;
    public function toPayload(): array;
    public static function eventName(): string;
}