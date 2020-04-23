<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RoleRelationshipManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = new $requestedName($container);
        if (!$manager instanceof RoleRelationshipManager) {
            throw new ServiceNotCreatedException(
                $requestedName . ' not an instance of ' . RoleRelationshipManager::class
            );
        }
        $manager->configure($container->get('Config')['acl_role_relationship_manager']);
        return $manager;
    }
}
