<?php

/**
 * Zelasli DI/IoC implementation
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Container
 */

namespace Zelasli\Container;

use Closure;

/**
 * DI/IoC interface for binding and resolving components.
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Container
 */
interface ContainerInterface
{
    /**
     * Register component to binding
     * 
     * Bind the component concrete to the container. An exception will be thrown
     * if identifier already exists and is shared.
     * 
     * @param string $identifier
     * @param null|string $concrete
     * @param bool $shared
     * 
     * @return void
     * @throws ContainerException
     */
    public function bind(
        string $identifier,
        Closure|null|string $concrete = null,
        bool $shared = false
    ): void;

    /**
     * Determines whether abstract is bound to component
     * 
     * @param string $abstract
     * 
     * @return bool
     */
    public function bound(string $abstract): bool;

    /**
     * Get binding component by its identifier
     * 
     * An exception will be thrown if the given identifier is not bound.
     * 
     * @param string $identifier
     * 
     * @return string
     * @throws ComponentNotFoundException
     */
    public function get(string $identifier): mixed;

    /**
     * Check whether the given identifier exists.
     * 
     * @param string $identifier
     * 
     * @return bool
     */
    public function has(string $identifier): bool;

    /**
     * Register an existing instance as shared component
     * 
     * This will not bind any concrete but store the instance only
     * 
     * @param string $identifier
     * @param object $instance
     * 
     * @return void
     * @throws ContainerException
     */
    public function instance(string  $identifier, object $instance): void;

    /**
     * Make bound component ready to use and return the instance
     * 
     * Tries to find component instance if any or get bound component by its 
     * identifier then resolve it and return the instance. The dependencies will
     * be resolved first if one exists as shared will be used unless the component
     * is not shared also.
     * 
     * @param string $identifier
     * @param array $parameters
     * 
     * @return object
     * @throws ContainerException
     * @throws ComponentNotFoundException
     */
    public function make(string $identifier, array $parameters = []): mixed;

    /**
     * Register a shared component
     * 
     * Bind the concrete as shared (singleton) component
     * 
     * @param string $identifier
     * @param Closure|null|string $concrete
     * 
     * @return void
     */
    public function singleton(
        string  $identifier,
        Closure|null|string $concrete = null
    ): void;
}
