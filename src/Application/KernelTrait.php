<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Application
 */

namespace Zelasli\Application;

use Zelasli\FrameworkKernel;
use Zelasli\Helpers;

trait KernelTrait
{
    /**
     * The application engine instance
     *
     * @var FrameworkKernel
     */
    protected ?FrameworkKernel $container = null;

    protected ControllerInterface $controller;

    /**
     * Constructor
     *
     * @param FrameworkKernel $container
     */
    final public function __construct(
        FrameworkKernel $container
    ) {
        $this->container = $container;
    }

    public function halt(): void
    {
        exit(0);
    }

    final public function handle(): self
    {
        $request = $this->container->getRequest();
        $router = $this->container->getRouter();
        $route = $router->findRouteByUrl($request->getRequestTarget());
        $controllerClass = $route->getClass();
        $this->controller = new $controllerClass($request);

        Helpers::inspectDie($this->controller);

        return $this;
    }

    /**
     * Initialize stuffs ready to use in the controller.
     *
     * @return void
     */
    final public function initialize(): void
    {
        $this->services();
    }

    /**
     * Register application servces
     *
     * @return void
     */
    public function services(): void {}

    final public function terminate(): void
    {
        // $this->controller->doSend();
        $this->halt();
    }
}
