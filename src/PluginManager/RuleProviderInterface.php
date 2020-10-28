<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Laminas\Permissions\Acl\Role\RoleInterface;
use Riddlestone\Brokkr\Acl\ResourceInterface;
use Riddlestone\Brokkr\Acl\RuleInterface;

interface RuleProviderInterface
{
    /**
     * @param RoleInterface[] $roles
     * @param ResourceInterface[] $resources
     * @return RuleInterface[]
     */
    public function getRules(array $roles, array $resources);
}
