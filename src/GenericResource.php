<?php

namespace Riddlestone\Brokkr\Acl;

use Laminas\Permissions\Acl\Resource\GenericResource as LaminasGenericResource;

class GenericResource extends LaminasGenericResource implements ResourceInterface
{
    /**
     * @var string|null
     */
    protected $parentResourceId;

    public function __construct($resourceId, $parentResourceId = null)
    {
        parent::__construct($resourceId);

        $this->parentResourceId = $parentResourceId;
    }

    public function getParentResourceId(): ?string
    {
        return $this->parentResourceId;
    }
}
