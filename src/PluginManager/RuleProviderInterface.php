<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Riddlestone\Brokkr\Acl\RuleInterface;

interface RuleProviderInterface
{
    /**
     * @param array $roles
     * @param array $resources
     * @return RuleInterface[]
     */
    public function getRules(array $roles, array $resources);
}
