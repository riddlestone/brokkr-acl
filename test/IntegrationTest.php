<?php

namespace Riddlestone\Brokkr\Acl\Test;

use Laminas\Config\Config;
use Laminas\Permissions\Acl\Acl as LaminasAcl;
use Laminas\Permissions\Acl\Role\GenericRole;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Acl\Acl;
use Riddlestone\Brokkr\Acl\Exception\ResourceNotFound;
use Riddlestone\Brokkr\Acl\Exception\RoleNotFound;
use Riddlestone\Brokkr\Acl\Module;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipProviderInterface;
use Riddlestone\Brokkr\Acl\PluginManager\RuleProviderInterface;
use Riddlestone\Brokkr\Acl\ResourceInterface;
use Riddlestone\Brokkr\Acl\RuleInterface;

class IntegrationTest extends TestCase
{
    /**
     * @throws ResourceNotFound
     * @throws RoleNotFound
     */
    public function testIntegration()
    {
        $module = new Module();
        $config = new Config($module->getConfig());
        $config->merge(
            new Config(
                [
                    'acl_role_manager' => [
                        'factories' => [
                            'child_role' => function () {
                                return new GenericRole('child_role');
                            },
                            'parent_role' => function () {
                                return new GenericRole('parent_role');
                            },
                        ],
                    ],
                    'acl_role_relationship_manager' => [
                        'factories' => [
                            'relationship_manager' => function () {
                                $manager = $this->createMock(RoleRelationshipProviderInterface::class);
                                $manager->method('getRoleParents')->willReturnCallback(
                                    function (RoleInterface $role) {
                                        return $role->getRoleId() === 'child_role' ? ['parent_role'] : [];
                                    }
                                );
                                return $manager;
                            },
                        ],
                        'providers' => [
                            'relationship_manager',
                        ],
                    ],
                    'acl_resource_manager' => [
                        'factories' => [
                            'child_resource' => function () {
                                $role = $this->createMock(ResourceInterface::class);
                                $role->method('getResourceId')->willReturn('child_resource');
                                $role->method('getParentResourceId')->willReturn('parent_resource');
                                return $role;
                            },
                            'parent_resource' => function () {
                                $role = $this->createMock(ResourceInterface::class);
                                $role->method('getResourceId')->willReturn('parent_resource');
                                $role->method('getParentResourceId')->willReturn(null);
                                return $role;
                            },
                        ],
                    ],
                    'acl_rule_manager' => [
                        'factories' => [
                            'rule_manager' => function () {
                                $manager = $this->createMock(RuleProviderInterface::class);
                                $manager->method('getRules')->willReturnCallback(
                                    function ($roles, $resources) {
                                        if (
                                            in_array(
                                                'parent_role',
                                                array_map(
                                                    function ($role) {
                                                        return $role ? $role->getRoleId() : null;
                                                    },
                                                    $roles
                                                )
                                            )
                                            && in_array(
                                                'parent_resource',
                                                array_map(
                                                    function ($resource) {
                                                        return $resource ? $resource->getResourceId() : null;
                                                    },
                                                    $resources
                                                )
                                            )
                                        ) {
                                            $rule = $this->createMock(RuleInterface::class);
                                            $rule->method('getRoleId')->willReturn('parent_role');
                                            $rule->method('getResourceId')->willReturn('parent_resource');
                                            $rule->method('getType')->willReturn(LaminasAcl::TYPE_ALLOW);
                                            $rule->method('getPrivilege')->willReturn('edit');
                                            $rule->method('getAssertion')->willReturn(null);
                                            return [$rule];
                                        }
                                        return [];
                                    }
                                );
                                return $manager;
                            },
                        ],
                        'providers' => [
                            'rule_manager',
                        ],
                    ],
                ]
            )
        );
        $serviceManager = new ServiceManager($config->toArray()['service_manager']);
        $serviceManager->setService('Config', $config->toArray());
        /** @var Acl $acl */
        $acl = $serviceManager->get(Acl::class);
        $this->assertTrue($acl->isAllowed('parent_role', 'parent_resource', 'edit'));
        $this->assertTrue($acl->isAllowed('child_role', 'parent_resource', 'edit'));
        $this->assertTrue($acl->isAllowed('parent_role', 'child_resource', 'edit'));
        $this->assertTrue($acl->isAllowed('child_role', 'child_resource', 'edit'));
        $this->assertFalse($acl->isAllowed('parent_role', 'parent_resource', 'delete'));
    }
}
