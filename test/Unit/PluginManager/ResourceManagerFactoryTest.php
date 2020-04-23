<?php

namespace Riddlestone\Brokkr\Acl\Test\Unit\PluginManager;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\PluginManager\ResourceManager;
use Riddlestone\Brokkr\Acl\PluginManager\ResourceManagerFactory;
use stdClass;

class ResourceManagerFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvalidInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new ResourceManagerFactory();
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
                        return ['acl_resource_manager' => []];
                    default:
                        throw new ServiceNotFoundException();
                }
            }
        );

        $factory = new ResourceManagerFactory();
        $acl = $factory($container, ResourceManager::class);
        $this->assertInstanceOf(ResourceManager::class, $acl);
    }
}
