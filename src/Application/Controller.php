<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Application
 */

namespace Zelasli\Application;

use Zelasli\Http\Message\Response;
use Zelasli\Http\Message\ServerRequest;

/**
 * @method void assign(string $name, mixed $value)
 * @method void set(array $data)
 */
class Controller implements ControllerInterface
{
    /**
     * Instance of HTTP Request object, that contains the information about the
     * current HTTP Request message.
     * 
     * @var \Zelasli\Http\ServerRequest
     */
    protected ServerRequest $request;

    /**
     * View template
     *
     * @var \Zelasli\Application\View
     */
    protected View $view;

    protected Response $response;

    /**
     * Constructor
     * 
     * @param \Zelasli\Http\ServerRequest $request
     */
    public function __construct(ServerRequest $request)
    {
        $this->request = $request;
    }
    
    public function initialize(): void {}
    
    final public function dispatch($action, $params = null): void
    {
        if (is_array($params) && !empty($params)) {
            $ret = $this->{$action}(...$params);
        } else {
            $ret = $this->{$action}();
        }

        if (!$ret instanceof Response) {
            $content = "";
            
            if ($ret instanceof View) {
                $content = $ret->getContent();
            } elseif (is_string($ret)) {
                $content = $ret;
            }

            $ret = new Response($content);
        }

        $this->response = $ret;
    }

    final public function doSend(): void
    {
        $this->response->send();
    }
}
