<?php

namespace Riddlestone\Brokkr\Acl\Test\Unit;

use Laminas\Permissions\Acl\Acl as LaminasAcl;
use Laminas\Permissions\Acl\Resource\ResourceInterface as LaminasResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\Acl;
use Riddlestone\Brokkr\Acl\Exception\ResourceNotFound;
use Riddlestone\Brokkr\Acl\Exception\RoleNotFound;
use Riddlestone\Brokkr\Acl\PluginManager\ResourceManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipManagerInterface;
use Riddlestone\Brokkr\Acl\PluginManager\RuleManagerInterface;
use Riddlestone\Brokkr\Acl\ResourceInterface;
use Riddlestone\Brokkr\Acl\RuleInterface;

class AclTest extends TestCase
{
    public function testGetAndSetAcl()
    {
        $acl = new Acl();
        $laminasAcl = $acl->getAcl();
        $this->assertInstanceOf(LaminasAcl::class, $laminasAcl);
        $this->assertTrue($laminasAcl === $acl->getAcl());
        $acl->setAcl(new LaminasAcl());
        $this->assertFalse($laminasAcl === $acl->getAcl());
    }

    public function testHasRole()
    {
        $acl = new Acl();
        $acl->setAcl(new LaminasAcl());
        $roleManager = $this->createMock(RoleManager::class);
        $roleManager->method('has')->willReturnCallback(
            function ($name) {
                return in_array(
                    $name,
                    [
                        'role_1',
                    ]
                );
            }
        );
        $acl->setRoleManager($roleManager);

        $this->assertTrue($acl->hasRole('role_1'));
        $this->assertFalse($acl->hasRole('role_2'));
    }

    /**
     * @throws RoleNotFound
     */
    public function testGetRole()
    {
        $acl = new Acl();

        $acl->setAcl(new LaminasAcl());

        $roleManager = $this->createMock(RoleManager::class);
        $roleManager->method('has')->willReturnCallback(
            function ($name) {
                return in_array(
                    $name,
                    [
                        'role_1',
                        'parent_1',
                    ]
                );
            }
        );
        $roleManager->method('get')->willReturnCallback(
            function ($name) {
                switch ($name) {
                    case 'role_1':
                        $mock = $this->createMock(RoleInterface::class);
                        $mock->method('getRoleId')->willReturn('role_1');
                        return $mock;
                    case 'parent_1':
                        $mock = $this->createMock(RoleInterface::class);
                        $mock->method('getRoleId')->willReturn('parent_1');
                        return $mock;
                }
                throw new ServiceNotFoundException();
            }
        );
        $acl->setRoleManager($roleManager);

        $relationshipManager = $this->createMock(RoleRelationshipManagerInterface::class);
        $relationshipManager->method('getRoleParents')->willReturnCallback(
            function ($role) {
                return $role->getRoleId() === 'role_1' ? ['parent_1'] : [];
            }
        );
        $acl->setRoleRelationshipManager($relationshipManager);

        $ruleManager = $this->createMock(RuleManagerInterface::class);
        $ruleManager->method('getRules')->willReturn([]);
        $acl->setRuleManager($ruleManager);

        $this->assertFalse($acl->getAcl()->hasRole('role_1'));
        $this->assertFalse($acl->getAcl()->hasRole('parent_1'));
        $this->assertEquals('role_1', $acl->getRole('role_1')->getRoleId());
        // getting a second time, to check we get the correct role now it is loaded
        $this->assertEquals('role_1', $acl->getRole('role_1')->getRoleId());
        $this->assertTrue($acl->getAcl()->hasRole('role_1'));
        $this->assertTrue($acl->getAcl()->hasRole('parent_1'));
        $this->expectException(RoleNotFound::class);
        $acl->getRole('role_not_found');
    }

    public function testHasResource()
    {
        $acl = new Acl();

        $acl->setAcl(new LaminasAcl());

        $resourceManager = $this->createMock(ResourceManager::class);
        $resourceManager->method('has')->willReturnCallback(
            function ($name) {
                return in_array(
                    $name,
                    [
                        'resource_1',
                    ]
                );
            }
        );
        $acl->setResourceManager($resourceManager);

        $this->assertTrue($acl->hasResource('resource_1'));
        $this->assertFalse($acl->hasResource('resource_2'));
    }

    /**
     * @throws ResourceNotFound
     */
    public function testGetResource()
    {
        $acl = new Acl();

        $acl->setAcl(new LaminasAcl());

        $resourceManager = $this->createMock(ResourceManager::class);
        $resourceManager->method('has')->willReturnCallback(
            function ($name) {
                return in_array(
                    $name,
                    [
                        'resource_1',
                        'parent_1',
                    ]
                );
            }
        );
        $resourceManager->method('get')->willReturnCallback(
            function ($name) {
                switch ($name) {
                    case 'resource_1':
                        $mock = $this->createMock(ResourceInterface::class);
                        $mock->method('getResourceId')->willReturn('resource_1');
                        $mock->method('getParentResourceId')->willReturn('parent_1');
                        return $mock;
                    case 'parent_1':
                        $mock = $this->createMock(ResourceInterface::class);
                        $mock->method('getResourceId')->willReturn('parent_1');
                        $mock->method('getParentResourceId')->willReturn(null);
                        return $mock;
                }
                throw new ServiceNotFoundException();
            }
        );
        $acl->setResourceManager($resourceManager);

        $ruleManager = $this->createMock(RuleManagerInterface::class);
        $ruleManager->method('getRules')->willReturn([]);
        $acl->setRuleManager($ruleManager);

        $this->assertFalse($acl->getAcl()->hasResource('resource_1'));
        $this->assertFalse($acl->getAcl()->hasResource('parent_1'));
        $this->assertEquals('resource_1', $acl->getResource('resource_1')->getResourceId());
        // getting a second time, to check we get the correct resource now it is loaded
        $this->assertEquals('resource_1', $acl->getResource('resource_1')->getResourceId());
        $this->assertTrue($acl->getAcl()->hasResource('resource_1'));
        $this->assertTrue($acl->getAcl()->hasResource('parent_1'));
        $this->expectException(ResourceNotFound::class);
        $acl->getResource('resource_not_found');
    }

    /**
     * @throws ResourceNotFound
     * @throws RoleNotFound
     */
    public function testIsAllowed()
    {
        $acl = new Acl();

        $acl->setAcl(new LaminasAcl());

        $roleManager = $this->createMock(RoleManager::class);
        $roleManager->method('has')->willReturn(true);
        $roleManager->method('get')->willReturnCallback(
            function ($name) {
                $mock = $this->createMock(RoleInterface::class);
                $mock->method('getRoleId')->willReturn($name);
                return $mock;
            }
        );
        $acl->setRoleManager($roleManager);

        $relationshipManager = $this->createMock(RoleRelationshipManagerInterface::class);
        $relationshipManager->method('getRoleParents')->willReturn([]);
        $acl->setRoleRelationshipManager($relationshipManager);

        $resourceManager = $this->createMock(ResourceManager::class);
        $resourceManager->method('has')->willReturn(true);
        $resourceManager->method('get')->willReturnCallback(
            function ($name) {
                $mock = $this->createMock(ResourceInterface::class);
                $mock->method('getResourceId')->willReturn($name);
                $mock->method('getParentResourceId')->willReturn(null);
                return $mock;
            }
        );
        $acl->setResourceManager($resourceManager);

        $ruleManager = $this->createMock(RuleManagerInterface::class);
        $ruleManager->method('getRules')->willReturnCallback(
            function ($roles, $resources) {
                $roles = array_map(
                    function ($role) {
                        return $role instanceof RoleInterface ? $role->getRoleId() : $role;
                    },
                    $roles
                );
                $resources = array_map(
                    function ($resource) {
                        return $resource instanceof LaminasResourceInterface ? $resource->getResourceId() : $resource;
                    },
                    $resources
                );
                if (!in_array('role_1', $roles) || !in_array('resource_1', $resources)) {
                    return [];
                }
                $rule = $this->createMock(RuleInterface::class);
                $rule->method('getType')->willReturn(LaminasAcl::TYPE_ALLOW);
                $rule->method('getRoleId')->willReturn('role_1');
                $rule->method('getResourceId')->willReturn('resource_1');
                $rule->method('getPrivilege')->willReturn(null);
                $rule->method('getAssertion')->willReturn(null);
                return [$rule];
            }
        );
        $acl->setRuleManager($ruleManager);

        $this->assertTrue($acl->isAllowed('role_1', 'resource_1'));
        $this->assertTrue($acl->isAllowed('role_1', 'resource_1', 'view'));
        $this->assertFalse($acl->isAllowed('role_1', 'resource_2'));
        $this->assertFalse($acl->isAllowed('role_2', 'resource_1'));
        $this->assertFalse($acl->isAllowed('role_2', 'resource_2'));
    }
}
