<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Laminas\ServiceManager\AbstractPluginManager;

abstract class AbstractProviderManager extends AbstractPluginManager
{
    /**
     * @var string[]
     */
    protected $providers = [];

    /**
     * @param array $config
     * @return static
     */
    public function configure(array $config)
    {
        if (isset($config['providers'])) {
            $this->providers = $config['providers'];
        }
        return parent::configure($config);
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return array_map([$this, 'get'], $this->providers);
    }
}
