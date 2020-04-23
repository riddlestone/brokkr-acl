<?php

namespace Riddlestone\Brokkr\Acl\Test\Unit;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\Acl;
use Riddlestone\Brokkr\Acl\AclFactory;
use Riddlestone\Brokkr\Acl\PluginManager\ResourceManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipManager;
use Riddlestone\Brokkr\Acl\PluginManager\RuleManager;
use stdClass;

class AclFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvalidInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new AclFactory();
        $this->expectException(ServiceNotCreatedException::class);
        $factory($container, stdClass::class);
    }

    /**
     * @throws ContainerException
     */
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(
            function ($name) {
                switch ($name) {
                    case 'Config':
                        return [];
                    case RoleManager::class:
                        return $this->createMock(RoleManager::class);
                    case RoleRelationshipManager::class:
                        return $this->createMock(RoleRelationshipManager::class);
                    case ResourceManager::class:
                        return $this->createMock(ResourceManager::class);
                    case RuleManager::class:
                        return $this->createMock(RuleManager::class);
                    default:
                        throw new ServiceNotFoundException();
                }
            }
        );

        $factory = new AclFactory();
        $acl = $factory($container, Acl::class);
        $this->assertInstanceOf(Acl::class, $acl);
        $this->assertInstanceOf(RoleManager::class, $acl->getRoleManager());
        $this->assertInstanceOf(RoleRelationshipManager::class, $acl->getRoleRelationshipManager());
        $this->assertInstanceOf(ResourceManager::class, $acl->getResourceManager());
        $this->assertInstanceOf(RuleManager::class, $acl->getRuleManager());
    }
}
