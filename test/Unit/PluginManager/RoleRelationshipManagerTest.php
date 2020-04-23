<?php

namespace Riddlestone\Brokkr\Acl\Test\Unit\PluginManager;

use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipManagerInterface;

class RoleRelationshipManagerTest extends TestCase
{
    public function testGetRoleParents()
    {
        $serviceManager = $this->createMock(ServiceManager::class);

        $pluginManager = new RoleRelationshipManager($serviceManager);
        $pluginManager->configure(
            [
                'factories' => [
                    'provider_1' => function () {
                        $provider = $this->createMock(RoleRelationshipManagerInterface::class);
                        $provider->method('getRoleParents')->willReturnCallback(
                            function ($role) {
                                return $role->getRoleId() === 'child_role' ? ['parent_role_1'] : [];
                            }
                        );
                        return $provider;
                    },
                    'provider_2' => function () {
                        $provider = $this->createMock(RoleRelationshipManagerInterface::class);
                        $provider->method('getRoleParents')->willReturnCallback(
                            function ($role) {
                                return $role->getRoleId() === 'child_role' ? ['parent_role_2'] : [];
                            }
                        );
                        return $provider;
                    },
                ],
                'providers' => ['provider_1', 'provider_2']
            ]
        );

        $this->assertCount(2, $pluginManager->getProviders());

        $childRole = $this->createMock(RoleInterface::class);
        $childRole->method('getRoleId')->willReturn('child_role');
        $this->assertEquals(['parent_role_1', 'parent_role_2'], $pluginManager->getRoleParents($childRole));
    }
}
