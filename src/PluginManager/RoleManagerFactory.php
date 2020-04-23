<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RoleManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = new $requestedName($container);
        if (!$manager instanceof RoleManager) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . RoleManager::class);
        }
        $manager->configure($container->get('Config')['acl_role_manager']);
        return $manager;
    }
}
