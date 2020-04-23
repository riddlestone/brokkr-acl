<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Laminas\Permissions\Acl\Role\RoleInterface;

class RoleRelationshipManager extends AbstractProviderManager implements RoleRelationshipManagerInterface
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = RoleRelationshipProviderInterface::class;

    /**
     * @param RoleInterface $role
     * @return string[]
     */
    public function getRoleParents(RoleInterface $role): array
    {
        $parents = [];
        foreach ($this->getProviders() as $provider) {
            $parents = array_merge($parents, $provider->getRoleParents($role));
        }
        return $parents;
    }
}
