<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli
 */

namespace Zelasli;

use Zelasli\Application\KernelInterface;
use Zelasli\Application\View;
use Zelasli\Http\Headers;
use Zelasli\Http\Message\Response;
use Zelasli\Http\Status;

class Helpers
{
    private static ?KernelInterface $app = null;

    private static $settings = null;

    /**
     * Abort the script and send a response to the client.
     *
     * @param int $code
     * @param string $message
     * @param array $headers
     *
     * @return void
     */
    public static function abort($code, $message = "", array $headers = [])
    {
        http_response_code($code);

        foreach ($headers as $header => $value) {
            header("$header: $value");
        }
        echo "$message";
        exit;
    }

    public static function app()
    {
        return self::$app;
    }

    public static function config($params, $default = null)
    {
        if (empty($params)) {
            return null;
        }

        if (is_string($params)) {
            $params = explode(".", $params);
        } elseif (!is_array($params)) {
            return null;
        }

        $config = self::$app->getConfig('*') ?? [];
        $data = array_change_key_case($config, CASE_LOWER);

        foreach ($params as $param) {
            $data = $data[strtolower($param)] ?? $default;
        }

        return $data;
    }

    /**
     * Initialize helper with settings
     *
     * @param \Zelasli\Application\KernelInterface $app
     * @param array $settings
     *
     * @return void
     */
    public static function init($app, $settings)
    {
        if (!self::$app) {
            self::$app = $app;
            self::$settings = $settings;
        }
    }

    /**
     * Inspect a variable
     *
     * Dumps information about a variable that includes its type and value 
     *
     * @var mixed $value
     *
     * @return void
     */
    public static function inspect($value): void
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }

    /**
     * Inspect a variable and die
     *
     * Dumps information about a variable that includes its type and value.
     * Similar to Helper::insect() function except that this function halt the
     * program execution
     *
     * @param mixed $value
     *
     * @return void
     */
    public static function inspectDie($value): void
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        exit(0);
    }

    public static function response(
        $content,
        $contentType = 'text/html',
        $status = Status::OK,
        $headers = [],
        $charset = 'utf8'
    ) {
        if (is_array($headers)) {
            $headers = new Headers($headers);
        } elseif (!$headers instanceof Headers) {
            $headers = [];
        }

        return new Response(
            content: $content,
            contentType: $contentType,
            status: $status,
            headers: $headers,
            charset: $charset
        );
    }

    public static function varPath($path = "/")
    {
        $baseDir = self::$app->getConfig('BASE_DIR');
        $varDir = rtrim($baseDir, '/') . '/var';

        if ($baseDir) {
            return $path == '/' ?
                rtrim($varDir, '/') . $path :
                rtrim($varDir, '/') . DIRECTORY_SEPARATOR . ltrim($path, '/');
        } else {
            return null;
        }
    }

    public static function view($name, $data = []) {
        $template = new View($name, self::$settings['TEMPLATES_DIR']);
        $template->set($data);

        return $template;
    }
}
