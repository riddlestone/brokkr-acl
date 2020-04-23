<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Laminas\Permissions\Acl\Role\RoleInterface;

interface RoleRelationshipProviderInterface
{
    /**
     * @param RoleInterface $role
     * @return string[]
     */
    public function getRoleParents(RoleInterface $role): array;
}
