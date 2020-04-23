<?php

namespace Riddlestone\Brokkr\Acl\PluginManager;

use Laminas\ServiceManager\AbstractPluginManager;
use Riddlestone\Brokkr\Acl\ResourceInterface;

class ResourceManager extends AbstractPluginManager
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = ResourceInterface::class;
}
