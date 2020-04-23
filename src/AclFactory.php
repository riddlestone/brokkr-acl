<?php

namespace Riddlestone\Brokkr\Acl;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AclFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $acl = new $requestedName();
        if (!$acl instanceof Acl) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . Acl::class);
        }
        $acl->setRoleManager($container->get(PluginManager\RoleManager::class));
        $acl->setRoleRelationshipManager($container->get(PluginManager\RoleRelationshipManager::class));
        $acl->setResourceManager($container->get(PluginManager\ResourceManager::class));
        $acl->setRuleManager($container->get(PluginManager\RuleManager::class));
        return $acl;
    }
}
