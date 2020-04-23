<?php

namespace Riddlestone\Brokkr\Acl\Test\Unit\PluginManager;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipManagerFactory;
use stdClass;

class RoleRelationshipManagerFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvalidInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new RoleRelationshipManagerFactory();
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
                        return ['acl_role_relationship_manager' => []];
                    default:
                        throw new ServiceNotFoundException();
                }
            }
        );

        $factory = new RoleRelationshipManagerFactory();
        $acl = $factory($container, RoleRelationshipManager::class);
        $this->assertInstanceOf(RoleRelationshipManager::class, $acl);
    }
}
