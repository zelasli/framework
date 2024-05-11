<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Application
 */

namespace Zelasli\Application;

interface ControllerInterface {
    /**
     * Dispatch controller's action
     * 
     * @param string $action
     * @param array $params
     * @param array $conf
     * 
     * @return void
     */
    public function dispatch(string $action, $params = null): void;

    /**
     * Initialization hook
     *
     * @return void
     */
    public function initialize();

    /**
     * Send tesponse to the client
     * 
     * @return void
     */
    public function doSend();
}
