<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Core
 */

namespace Zelasli\Core;

use Composer\Autoload\ClassLoader;
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
     * Get configurations and setup container
     * 
     * @return void
     */
    protected function configure(): void
    {
        global $loader;
        $settings = $this->processSettings();
        $modules = $settings['MODULES_NAMESPACE'];
        
        $debugger = $this->make('Debugger', $settings['DEBUG']);
        $debugger->initialize();

        if (isset($loader) && $loader instanceof ClassLoader) {
            // Register module namespace not installed by composer
            foreach ($modules as $namespace => $directory) {
                $namespace = rtrim($namespace, '\\') . '\\';
                
                $loader->addPsr4($namespace, $directory);
            }
        }

        $this->processRouting($settings['BASE_DIR']);

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

        $this->configure();
    }

    /**
     * Configure routing to the container
     * 
     * @param string $baseDir
     * 
     * @return void
     */
    public function processRouting($baseDir): void
    {
        if (is_file($routesFileDir = $baseDir . '/conf/routes.php')) {
            $routeBuilder = $this->make('RouteBuilder');
    
            require_once $routesFileDir;
        }
    }

    /**
     * Get conf settings
     * 
     * @return <string, mixed>
     */
    protected function processSettings(): array
    {
        static $set = false;
        static $settings = [];

        if (!$set) {
            if (file_exists($file = dirname(__DIR__, 4) . '/conf/settings.php')) {
                $set = true;

                include_once $file;

                $settings += $config;
            }
        }

        return $settings;
    }
}
