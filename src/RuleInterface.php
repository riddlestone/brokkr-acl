<?php

namespace Riddlestone\Brokkr\Acl;

use Laminas\Permissions\Acl\Acl as LaminasAcl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;

interface RuleInterface
{
    /**
     * Returns one of {@link LaminasAcl::TYPE_ALLOW} or {@link LaminasAcl::TYPE_DENY}
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Returns the id of the role for whom this permission is granted or denied
     *
     * @return string|null
     */
    public function getRoleId(): ?string;

    /**
     * Returns the id of the resource to which this permission applies
     *
     * @return string|null
     */
    public function getResourceId(): ?string;

    /**
     * Returns the name of the privilege granted or denied
     *
     * @return string|null
     */
    public function getPrivilege(): ?string;

    /**
     * Returns the additional assertion applied to permission checks on this rule
     *
     * @return AssertionInterface|null
     */
    public function getAssertion(): ?AssertionInterface;
}
