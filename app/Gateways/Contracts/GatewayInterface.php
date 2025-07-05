<?php

namespace App\Gateways\Contracts;

interface GatewayInterface
{
    public function charge(array $data): mixed;
    public function verify(string $reference): bool;
    public function refund(string $transactionId): bool;
}
