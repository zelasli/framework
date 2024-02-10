<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Core
 */

namespace Zelasli\Core;

use Closure;
use Zelasli\Container\ComponentNotFoundException;
use Zelasli\Container\ContainerException;

/**
 * Abstract DI/IoC class for FrameworkKernel to serve as Container
 * 
 * Support feature for autowiring of components and enabling singleton feature.
 */
trait ContainerBundleTrait
{
    /**
     * Global shared (singleton) container instance
     * 
     * @var FrameworkKernel
     */
    protected static ?FrameworkKernel $instance = null;

    /**
     * Alias name for binding components
     * 
     * @var <string, string>
     */
    protected array $aliases;

    /**
     * Binding components list
     * 
     * @var <string, array>
     */
    protected array $bindings;

    /**
     * Resolved components binding instances
     * 
     * @var <string, object>
     */
    protected array $components;

    /**
     * Add alias name to binding component
     * 
     * @param string $name
     * @param string $abstract
     * 
     * @return void
     */
    public function alias(string $name, string $abstract): void
    {
        if ($this->bound($abstract)) {
            $abstract = $this->get($abstract);
        } else {
            $this->singleton($abstract);
        }

        $this->aliases[$name] = $abstract;
    }
    
    public function bind(
        string $identifier,
        Closure|null|string $concrete = null,
        bool $shared = false
    ): void {
        if (is_null($concrete)) {
            $concrete = $identifier;
        }
        
        if ($this->bound($identifier) && $this->isShared($identifier)) {
            throw new ContainerException(
                "`{$identifier}` already bound to other component."
            );
        }

        $component['concrete'] = $concrete;
        if ($shared) {
            $component['shared'] = $shared;
        }

        $this->bindings[$identifier] = $component;
    }

    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }

    public function get(string $identifier): mixed
    {
        if (isset($this->aliases[$identifier])) {
            return $this->aliases[$identifier];
        } elseif (isset($this->bindings[$identifier])) {
            return $identifier;
        }

        throw new ComponentNotFoundException("No component bound with identifier: ({$identifier})");
    }

    public function has(string $identifier): bool
    {
        return isset($this->components[$identifier]) || 
            isset($this->bindings[$identifier]);
    }

    public function instance(string $identifier, object $instance): void
    {
        $this->components[$identifier] = $instance;
    }

    /**
     * Check whether the given name is alias
     * 
     * @param string $name
     * 
     * @return bool
     */
    public function isAlias($name): bool
    {
        return isset($this->aliases[$name]);
    }

    /**
     * Check whether the given name is singleton component
     * 
     * @param string $abstract
     * 
     * @return bool
     */
    public function isShared($abstract): bool
    {
        return isset($this->components[$abstract]) || (
            isset($this->bindings[$abstract]['shared']) && 
            $this->bindings[$abstract]['shared']
        );
    }

    public function make(string $identifier, array $parameters = []): mixed
    {
        // TODO: retrun instance component by its identifier or relove one and
        // retrun it.
    }

    public function singleton(
        string  $identifier,
        Closure|null|string $concrete = null
    ): void {
        $this->bind($identifier, $concrete, true);
    }
}
