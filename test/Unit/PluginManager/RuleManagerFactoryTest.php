<?php

namespace Riddlestone\Brokkr\Acl\Test\Unit\PluginManager;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\PluginManager\RuleManager;
use Riddlestone\Brokkr\Acl\PluginManager\RuleManagerFactory;
use stdClass;

class RuleManagerFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvalidInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new RuleManagerFactory();
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
                        return ['acl_rule_manager' => []];
                    default:
                        throw new ServiceNotFoundException();
                }
            }
        );

        $factory = new RuleManagerFactory();
        $acl = $factory($container, RuleManager::class);
        $this->assertInstanceOf(RuleManager::class, $acl);
    }
}
