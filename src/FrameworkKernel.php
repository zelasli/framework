<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Core
 */

namespace Zelasli\Core;

use Zelasli\Container\ContainerInterface as IContainer;
use Zelasli\Debugger\Engine as Debugger;
use Zelasli\Routing\RouteBuilder;
use Zelasli\Routing\Router;

/**
 * Final Zelasli core framework component
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Core
 */
final class FrameworkKernel implements IContainer
{
    use ContainerBundleTrait;

    /**
     * FrameworkKernel constructor
     * 
     * Singleton, to protect outsiders from instanciating it.
     */
    protected function __construct() {
        $this->bindings = [];
        $this->aliases = [];
        $this->components = [];
    }

    /**
     * Get the global container instance or initial and get one
     * 
     * @return self
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initialize container by binding its component and configure it.
     * 
     * @return void
     */
    public function initialize(): void
    {
        $this->instance(self::class, self::$instance);
        $this->alias('container', self::class);
        $this->singleton(Debugger::class);
        $this->alias('Debugger', Debugger::class);
        $this->alias('RouteBuilder', RouteBuilder::class);
        $this->alias('Router', Router::class);
    }
}
