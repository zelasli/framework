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
use Zelasli\Routing\Route;

trait KernelTrait
{
    /**
     * The application engine instance
     *
     * @var FrameworkKernel
     */
    protected ?FrameworkKernel $container = null;

    protected ?Route $matchedRoute;

    protected object $controller;

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
        $this->matchedRoute = $router->findRouteByUrl(
            $request->getRequestTarget()
        );

        if ($this->matchedRoute) {
            $controllerClass = $this->matchedRoute->getClass();
            $this->controller = new $controllerClass($request);

            if ($this->controller instanceof ControllerInterface) {
                $this->controller->initialize();
                $this->controller->dispatch(
                    $this->matchedRoute->getAction(),
                    $this->matchedRoute->getParams()
                );
            } else {
                $this->controller->{$this->matchedRoute->getAction()}(
                    ...$this->matchedRoute->getParams()
                );
            }
        } else {
            Helpers::abort(404, "Not Found");
        }

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
     * Register application services
     *
     * @return void
     */
    public function services(): void {}

    final public function terminate(): void
    {
        if ($this->controller instanceof ControllerInterface) {
            $this->controller->doSend();
        }

        $this->halt();
    }
}
