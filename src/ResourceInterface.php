<?php

namespace Riddlestone\Brokkr\Acl;

use Laminas\Permissions\Acl\Resource\ResourceInterface as LaminasResourceInterface;

interface ResourceInterface extends LaminasResourceInterface
{
    public function getParentResourceId(): ?string;
}
