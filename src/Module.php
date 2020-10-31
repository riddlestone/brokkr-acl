<?php

namespace Riddlestone\Brokkr\Acl;

class Module
{
    public function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }
}
