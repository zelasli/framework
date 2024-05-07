<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli
 */

namespace Zelasli;

use Zelasli\Http\Message\ServerRequest;
use Zelasli\Http\Message\ServerRequestInterface;
use Zelasli\Routing\RouteBuilder;
use Zelasli\Routing\Router;

class FrameworkKernel {
    protected static ?self $static = null;

    private ?ServerRequestInterface $request = null;

    private ?Router $router = null;

    /**
     * System configuration settings
     * 
     * @var array
     */
    private ?array $settings = null;

    protected function __construct() {}

    /**
     * @return $this
     */
    public static function getInstance(): self
    {
        if (is_null(self::$static)) {
            self::$static =  new self;
        }

        return self::$static;
    }

    public function initialize()
    {
        Session::start();

        $this->initRequest();
        $this->initSettings();
        $this->initRouting($this->settings['BASE_DIR']);
    }

    /**
     * Process HTTP Request and ready to submit to application
     * 
     * @return ServerRequest
     */
    protected function initRequest(): void
    {
        $query = $_GET; // GET[]
        $data = $_POST; // POST[]
        $files = $_FILES ?? []; // POST[]
        $server = $_SERVER ?? []; // POST[]
        $env = $_ENV ?? []; // POST[]
        $cookie = $_COOKIE ?? []; // POST[]
        $session = $_SESSION ?? []; // POST[]

        $this->request = new ServerRequest(
            method: $_SERVER['REQUEST_METHOD'],
            target: $_SERVER['REQUEST_URI'],
            query: $query,
            data: $data,
            files: $files,
            server: $server,
            env: $env,
            cookie: $cookie,
            session: $session,
        );
    }

    /**
     * Configure routing to the container
     * 
     * @param string $baseDir
     * 
     * @return void
     */
    protected function initRouting($baseDir): void
    {
        if (is_file($routesFileDir = $baseDir . '/conf/routes.php')) {
            $routeBuilder = new RouteBuilder;
            $this->router = $routeBuilder->getRouterInstance();

            require_once $routesFileDir;
        }
    }

    /**
     * Get conf settings
     * 
     * @return <string, mixed>
     */
    protected function initSettings(): void
    {
        if (is_null($this->settings)) {
            $this->settings = [];

            // TODO: File not exists? exit!
            if (file_exists($file = dirname(__DIR__, 4) . '/conf/settings.php')) {
                $set = true;
                
                include_once $file;
                
                $this->settings += $config;
            }
            // TODO: Check that required conf attribute exist, else exit!
        }
    }

    public function startApp($appClass)
    {
        return new $appClass($this);
    }
}
