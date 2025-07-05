<?php

namespace App\Traits;

use App\Gateways\Contracts\GatewayInterface;

trait HasGatewayDriver
{
    public function resolveDriver(): ?GatewayInterface
    {
        $driverClass = $this->driver_class;

        if (!$driverClass || !class_exists($driverClass)) {
            return null;
        }

        return new $driverClass($this);
    }
}
