<?php

namespace Riddlestone\Brokkr\Acl;

use Laminas\Permissions\Acl\Assertion\AssertionInterface;

class GenericRule implements RuleInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $roleId;

    /**
     * @var string|null
     */
    protected $resourceId;

    /**
     * @var string|null
     */
    protected $privilege;

    /**
     * @var AssertionInterface|null
     */
    protected $assertion;

    public function __construct(
        string $type,
        ?string $roleId = null,
        ?string $resourceId = null,
        ?string $privilege = null,
        ?AssertionInterface $assertion = null
    ) {
        $this->type = $type;
        $this->roleId = $roleId;
        $this->resourceId = $resourceId;
        $this->privilege = $privilege;
        $this->assertion = $assertion;
    }

    /**
     * Returns one of {@link LaminasAcl::TYPE_ALLOW} or {@link LaminasAcl::TYPE_DENY}
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns the id of the role for whom this permission is granted or denied
     *
     * @return string|null
     */
    public function getRoleId(): ?string
    {
        return $this->roleId;
    }

    /**
     * Returns the id of the resource to which this permission applies
     *
     * @return string|null
     */
    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    /**
     * Returns the name of the privilege granted or denied
     *
     * @return string|null
     */
    public function getPrivilege(): ?string
    {
        return $this->privilege;
    }

    /**
     * Returns the additional assertion applied to permission checks on this rule
     *
     * @return AssertionInterface|null
     */
    public function getAssertion(): ?AssertionInterface
    {
        return $this->assertion;
    }
}
