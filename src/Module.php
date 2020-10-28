<?php

namespace Riddlestone\Brokkr\Acl;

use Laminas\Mvc\MvcEvent;
use Laminas\View\Helper\Navigation;
use Laminas\View\Renderer\PhpRenderer;

class Module
{
    public function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }

    /**
     * Register {@link onRender} with the application event manager
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event): void
    {
        $event->getApplication()->getEventManager()->attach('render', [$this, 'onRender']);
    }

    /**
     * Inject the ACL into {@link Navigation} when rendering
     *
     * @param MvcEvent $event
     */
    public function onRender(MvcEvent $event): void
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $renderer = $serviceManager->get('ViewRenderer');
        if (!$renderer instanceof PhpRenderer) {
            trigger_error('"ViewRenderer" not an instance of ' . PhpRenderer::class, E_USER_NOTICE);
            return;
        }

        $navigation = $renderer->getHelperPluginManager()->get('navigation');
        if (!$navigation instanceof Navigation) {
            trigger_error('"navigation" not an instance of ' . Navigation::class, E_USER_NOTICE);
            return;
        }

        $navigation->setAcl($serviceManager->get(Acl::class));
    }
}
