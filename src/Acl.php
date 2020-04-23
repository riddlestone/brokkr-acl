<?php

namespace Riddlestone\Brokkr\Acl;

use Laminas\Permissions\Acl\Acl as LaminasAcl;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface as LaminasResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface as LaminasRoleInterface;
use Riddlestone\Brokkr\Acl\Exception\ResourceNotFound;
use Riddlestone\Brokkr\Acl\Exception\RoleNotFound;
use Riddlestone\Brokkr\Acl\PluginManager\ResourceManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleManager;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipManagerInterface;
use Riddlestone\Brokkr\Acl\PluginManager\RuleManagerInterface;

class Acl implements AclInterface
{
    // region Properties

    /**
     * @var LaminasAcl
     */
    protected $acl;

    /**
     * @param LaminasAcl $acl
     */
    public function setAcl(LaminasAcl $acl): void
    {
        $this->acl = $acl;
    }

    /**
     * @return LaminasAcl
     */
    public function getAcl(): LaminasAcl
    {
        if (!$this->acl) {
            $this->acl = new LaminasAcl();
        }
        return $this->acl;
    }

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * @param RoleManager $roleManager
     */
    public function setRoleManager(RoleManager $roleManager): void
    {
        $this->roleManager = $roleManager;
    }

    /**
     * @return RoleManager
     */
    public function getRoleManager(): RoleManager
    {
        return $this->roleManager;
    }

    /**
     * @var RoleRelationshipManagerInterface
     */
    protected $relationshipManager;

    /**
     * @param RoleRelationshipManagerInterface $relationshipManager
     */
    public function setRoleRelationshipManager(RoleRelationshipManagerInterface $relationshipManager): void
    {
        $this->relationshipManager = $relationshipManager;
    }

    /**
     * @return RoleRelationshipManagerInterface
     */
    public function getRoleRelationshipManager(): RoleRelationshipManagerInterface
    {
        return $this->relationshipManager;
    }

    /**
     * @var ResourceManager|null
     */
    protected $resourceManager;

    /**
     * @param ResourceManager|null $resourceManager
     */
    public function setResourceManager(?ResourceManager $resourceManager): void
    {
        $this->resourceManager = $resourceManager;
    }

    /**
     * @return ResourceManager|null
     */
    public function getResourceManager(): ?ResourceManager
    {
        return $this->resourceManager;
    }

    /**
     * @var RuleManagerInterface
     */
    protected $ruleManager;

    /**
     * @param RuleManagerInterface $ruleManager
     */
    public function setRuleManager(RuleManagerInterface $ruleManager): void
    {
        $this->ruleManager = $ruleManager;
    }

    /**
     * @return RuleManagerInterface
     */
    public function getRuleManager(): RuleManagerInterface
    {
        return $this->ruleManager;
    }

    /**
     * Have null role and resource rules been loaded?
     *
     * @var bool
     */
    protected $nullRulesLoaded = false;

    // endregion Properties

    // region Roles

    /**
     * @param string|LaminasRoleInterface $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->getAcl()->hasRole($role)
            || (
                is_string($role)
                && $this->getRoleManager()->has($role)
            )
            || (
                $role instanceof LaminasRoleInterface
                && $this->getRoleManager()->has($role->getRoleId())
            );
    }

    /**
     * Loads a role from the roleManager into the Acl
     *
     * @param string $role
     * @return LaminasRoleInterface
     * @throws RoleNotFound
     */
    public function getRole($role)
    {
        if ($this->getAcl()->hasRole($role)) {
            return $this->getRoleManager()->get($role);
        }
        if (!$this->getRoleManager()->has($role)) {
            throw new RoleNotFound($role . ' could not be found in ' . RoleManager::class);
        }
        $roleObject = $this->getRoleManager()->get($role);
        $roleParents = array_map([$this, 'getRole'], $this->getRoleRelationshipManager()->getRoleParents($roleObject));
        $this->getAcl()->addRole($roleObject, $roleParents);
        $this->loadRules(
            [$roleObject],
            array_merge(
                [null],
                array_map([$this->getAcl(), 'getResource'], $this->getAcl()->getResources())
            )
        );
        return $roleObject;
    }

    // endregion Roles

    // region Resources

    /**
     * @inheritDoc
     */
    public function hasResource($resource)
    {
        return $this->getAcl()->hasResource($resource)
            || (
                is_string($resource)
                && $this->getResourceManager()->has($resource)
            )
            || (
                $resource instanceof LaminasResourceInterface
                && $this->getResourceManager()->has($resource->getResourceId())
            );
    }

    /**
     * Loads a resource from the resourceManager into the Acl
     *
     * @param string $resource
     * @return LaminasResourceInterface
     * @throws ResourceNotFound
     */
    public function getResource($resource)
    {
        if ($this->getAcl()->hasResource($resource)) {
            return $this->getResourceManager()->get($resource);
        }
        if (!$this->getResourceManager()->has($resource)) {
            throw new ResourceNotFound($resource . ' could not be found in ' . ResourceManager::class);
        }
        $resourceObject = $this->getResourceManager()->get($resource);
        $resourceParent = $resourceObject->getParentResourceId()
            ? $this->getResource($resourceObject->getParentResourceId())
            : null;
        $this->getAcl()->addResource($resourceObject, $resourceParent);
        $this->loadRules(
            array_merge([null], array_map([$this->getAcl(), 'getRole'], $this->getAcl()->getRoles())),
            [$resourceObject]
        );
        return $resourceObject;
    }

    // endregion Resources

    // region Rules

    public function addRule(RuleInterface $rule)
    {
        $this->getAcl()->setRule(
            LaminasAcl::OP_ADD,
            $rule->getType(),
            $rule->getRoleId(),
            $rule->getResourceId(),
            $rule->getPrivilege(),
            $rule->getAssertion()
        );
    }

    /**
     * @param array $roles
     * @param array $resources
     */
    public function loadRules(array $roles, array $resources)
    {
        foreach ($this->getRuleManager()->getRules($roles, $resources) as $rule) {
            $this->addRule($rule);
        }
    }

    // endregion Rules

    /**
     * @inheritDoc
     * @throws RoleNotFound
     * @throws ResourceNotFound
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        if (!$this->nullRulesLoaded) {
            $this->loadRules([null], [null]);
            $this->nullRulesLoaded = true;
        }
        if ($role !== null) {
            $role = $this->getRole(is_string($role) ? $role : $role->getRoleId());
        }
        if ($resource !== null) {
            $resource = $this->getResource(is_string($resource) ? $resource : $resource->getResourceId());
        }
        return $this->getAcl()->isAllowed($role, $resource, $privilege);
    }
}
