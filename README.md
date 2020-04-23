# Riddlestone Brokkr-Acl

A [Laminas](https://github.com/laminas) module to enable selectively populating
[Laminas ACL](https://github.com/laminas/laminas-permissions-acl) at query time 

Sometimes your project has a LOT of roles, resources and rules. This module has plugin managers for those things, and
only requests and loads into the ACL the minimum objects needed to answer a query.

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
composer require riddlestone/brokkr-acl
```

## Usage

This module adds four plugin managers, your module/project can provide configuration to any of these. 

### RoleManager

To provide roles for the ACL, implement `Laminas\ServiceManager\FactoryInterface`, and then add your class to the
`acl_role_manager` config:

```php
<?php
return [
    'acl_role_manager' => [
        'abstract_factories' => [
            'My\\Factory',
        ],
    ],
];
```

When the ACL requires a role, your factory will be asked if it can provide it.

### RoleRelationshipManager

To allow projects and modules to define the relationship between roles, the role relationship manager gathers
information about the parents of a given role.

To provide parentage information about roles, implement `RoleRelationshipProviderInterface`, and register the class as a
provider in the `acl_role_relationship_manager` config:

```php
<?php
return [
    'acl_role_relationship_manager' => [
        'factories' => [
            'My\\Provider' => 'My\\Provider\\Factory',
        ],
        'providers' => [
            'My\\Provider',
        ],
    ],
];
```

Specifying the providers separately from the factories which produce them allow us to use providers which are created
through abstract factories, or otherwise injected into the plugin manager.

### ResourceManager

To provide resources for the ACL, implement `Laminas\ServiceManager\FactoryInterface`, and then add your class to the
`acl_resource_manager` config:

```php
<?php
return [
    'acl_resource_manager' => [
        'abstract_factories' => [
            'My\\Factory',
        ],
    ],
];
```

When the ACL requires a resource, your factory will be asked if it can provide it.

### RuleManager

To allow projects and modules to specify permissions, the rule manager gathers information about which rules apply to
given roles and resources.

To provide rules, implement `RuleProviderInterface`, and register the class as a provider in the
`acl_rule_manager` config:

```php
<?php
return [
    'acl_rule_manager' => [
        'factories' => [
            'My\\Provider' => 'My\\Provider\\Factory',
        ],
        'providers' => [
            'My\\Provider',
        ],
    ],
];
```

Specifying the providers separately from the factories which produce them allow us to use providers which are created
through abstract factories, or otherwise injected into the plugin manager.

## Querying the ACL

Once all the managers are configured, you can query the ACL by pulling it from the ServiceManager, and calling
`isAllowed()`:

```php
<?php
use Laminas\ServiceManager\ServiceManager;
use Riddlestone\Brokkr\Acl\Acl;
/**
 * @var ServiceManager $serviceManager
 */
$acl = $serviceManager->get(Acl::class);
$isAllowed = $acl->isAllowed('some_role', 'a_resource', 'view');
```
