<?php

namespace Riddlestone\Brokkr\Acl;

return [
    'acl_resource_manager' => [],
    'acl_role_manager' => [],
    'acl_role_relationship_manager' => [],
    'acl_rule_manager' => [],
    'service_manager' => [
        'factories' => [
            Acl::class => AclFactory::class,
            PluginManager\ResourceManager::class => PluginManager\ResourceManagerFactory::class,
            PluginManager\RoleManager::class => PluginManager\RoleManagerFactory::class,
            PluginManager\RoleRelationshipManager::class => PluginManager\RoleRelationshipManagerFactory::class,
            PluginManager\RuleManager::class => PluginManager\RuleManagerFactory::class,
        ],
    ],
];
