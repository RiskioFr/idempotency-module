<?php
declare(strict_types=1);

namespace Riskio\IdempotencyModule;

class ConfigProvider
{
    public function __invoke() : array
    {
        $config = (new Module())->getConfig();

        return [
            'dependencies' => $config['service_manager'],
        ];
    }
}
