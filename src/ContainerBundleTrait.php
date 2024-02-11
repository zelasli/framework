<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Core
 */

namespace Zelasli\Core;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
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

        if ($this->isAlias($alias = $identifier) && 
        isset($this->components[$this->aliases[$alias]])) {
            return $this->components[$this->aliases[$alias]];
        }

        if (isset($this->components[$identifier])) {
            return $this->components[$identifier];
        }
        
        try {
            $component = $this->get($identifier);
        } catch (ComponentNotFoundException $cne) {
            throw $cne;
        }

        try {
            $reflectionClass = new ReflectionClass($component);
            
            $instance = $this->resolve($reflectionClass, $parameters);

            return $this->components[$reflectionClass->getName()] = $instance;
        } catch (ContainerException $ce) {
            throw $ce;
        } catch (ReflectionException $re) {
            throw new ContainerException($re->getMessage(), $re->getCode(), $re);
        }
    }

    /**
     * Resolve component or dependency by reflection to auto-wire dependencies
     * 
     * @param string $identifier
     * @param array $parameters
     * 
     * @return mixed
     */
    protected function resolve(
        ReflectionClass $reflectionClass,
        array $parameters
    ): mixed {
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException(
                "The class {$reflectionClass->getName()} is not instantiable"
            );
        }
        $component = $reflectionClass->getName();
        $reflectionMethod = $reflectionClass->getConstructor();
        $dependencies = $reflectionMethod ? 
            $reflectionMethod->getParameters() : [];

        if (empty($dependencies)) {
            return new $component();
        }
        
        $result = [];
        foreach ($dependencies as $dependency) {
            $type = $dependency->getType();
            if ($type instanceof ReflectionUnionType) {
                throw new ContainerException(
                    "Argument of union type not supported"
                );
            }
            if ($type !== null && !$type instanceof ReflectionNamedType) {
                throw new ContainerException(
                    "Unsupported argument type:" . get_class($type)
                );
            }elseif (!$type instanceof ReflectionNamedType) {
                throw new ContainerException(
                    "Parameter ({$dependency->getName()}) in {$component}".
                    "::__construct() must be specified"
                );
            }

            $dependencyName = trim((string) $type, '?');
            $isClass = (
                $type instanceof ReflectionNamedType && 
                !$type->isBuiltin()
            );

            switch (true) {
                // match postional argument
                case array_key_exists($dependency->getPosition(), $parameters):
                    $result[] = $parameters[$dependency->getPosition()];
                    break;
                // match named argument
                case array_key_exists($dependency->getName(), $parameters):
                    $result[] = $parameters[$dependency->getName()];
                    break;
                // auto-wire
                case $isClass:
                    $result[] = $this->has($dependencyName) ?
                    $this->make($dependencyName) :
                    $this->resolve(new ReflectionClass($dependencyName), []);
                    break;
                // is optional and has override? set it.
                case $dependency->isOptional() && (
                    array_key_exists($dependency->getPosition(), $parameters) ||
                    array_key_exists($dependency->getName(), $parameters)
                ):
                    $result[] = $parameters[$dependency->getPosition()] ??
                    $parameters[$dependency->getName()];
                    break;
                default:
                    throw new ContainerException(
                        "Provide value for ({$dependency->getName()}) ".
                        "argument in {$component}::__construct()"
                    );
                    break;
            }
        }

        return new $component(...$result);
    }

    public function singleton(
        string  $identifier,
        Closure|null|string $concrete = null
    ): void {
        $this->bind($identifier, $concrete, true);
    }
}
