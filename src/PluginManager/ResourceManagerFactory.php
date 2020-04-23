<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ResourceManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = new $requestedName($container);
        if (!$manager instanceof ResourceManager) {
            throw new ServiceNotCreatedException($requestedName . ' is not an instance of ' . ResourceManager::class);
        }
        $manager->configure($container->get('Config')['acl_resource_manager']);
        return $manager;
    }
}
