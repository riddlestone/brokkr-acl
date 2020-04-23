<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RuleManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = new $requestedName($container);
        if (!$manager instanceof RuleManager) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . RuleManager::class);
        }
        $manager->configure($container->get('Config')['acl_rule_manager']);
        return $manager;
    }
}
