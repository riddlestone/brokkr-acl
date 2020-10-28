<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Laminas\Permissions\Acl\Role\RoleInterface;
use Riddlestone\Brokkr\Acl\ResourceInterface;
use Riddlestone\Brokkr\Acl\RuleInterface;

class RuleManager extends AbstractProviderManager implements RuleManagerInterface
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = RuleProviderInterface::class;

    /**
     * @param RoleInterface[] $roles
     * @param ResourceInterface[] $resources
     * @return RuleInterface[]
     */
    public function getRules(array $roles, array $resources)
    {
        $rules = [];
        foreach ($this->getProviders() as $provider) {
            $rules = array_merge($rules, $provider->getRules($roles, $resources));
        }
        return $rules;
    }
}
