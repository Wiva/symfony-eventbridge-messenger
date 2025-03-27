<?php

namespace Wiva\EventBridgeMessenger;

interface EventBridgeEvent
{
    public function toHeaders(): ?array;
    public function toPayload(): array;
    public static function eventName(): string;
}