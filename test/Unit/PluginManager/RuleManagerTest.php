<?php

namespace Riddlestone\Brokkr\Acl\Test\Unit\PluginManager;

use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\PluginManager\RuleManager;
use Riddlestone\Brokkr\Acl\PluginManager\RuleProviderInterface;
use Riddlestone\Brokkr\Acl\ResourceInterface;
use Riddlestone\Brokkr\Acl\RuleInterface;

class RuleManagerTest extends TestCase
{
    public function testGetRules()
    {
        $serviceManager = $this->createMock(ServiceManager::class);

        $pluginManager = new RuleManager($serviceManager);

        $rule1 = $this->createMock(RuleInterface::class);
        $rule2 = $this->createMock(RuleInterface::class);

        $pluginManager->configure(
            [
                'factories' => [
                    'provider_1' => function () use ($rule1) {
                        $provider = $this->createMock(RuleProviderInterface::class);
                        $provider->method('getRules')->willReturn([$rule1]);
                        return $provider;
                    },
                    'provider_2' => function () use ($rule2) {
                        $provider = $this->createMock(RuleProviderInterface::class);
                        $provider->method('getRules')->willReturn([$rule2]);
                        return $provider;
                    },
                ],
                'providers' => ['provider_1', 'provider_2']
            ]
        );

        $this->assertCount(2, $pluginManager->getProviders());

        $roles = [$this->createMock(RoleInterface::class)];
        $resources = [$this->createMock(ResourceInterface::class)];
        $this->assertEquals([$rule1, $rule2], $pluginManager->getRules($roles, $resources));
    }
}
