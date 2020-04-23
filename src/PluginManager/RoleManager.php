<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\ServiceManager\AbstractPluginManager;

class RoleManager extends AbstractPluginManager
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = RoleInterface::class;
}
