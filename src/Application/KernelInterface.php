<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Application
 */

namespace Zelasli\Application;

interface KernelInterface {
    /**
     * Retrieve configuration settings data
     *
     * @param array|string $params
     *
     * @return mixed
     */
    public function getConfig($params = null): mixed;

    /**
     * Stopping the Application and exit the program
     *
     * @return void
     */
    public function halt(): void;

    /**
     * This stage application response will process the matched route and 
     * initializes the controller ready to dispatch and the send out the 
     * response
     *
     * @return $this
     */
    public function handle(): self;

    /**
     * Halt the application after the controller is processed and the response
     * is ready
     * 
     * @return void
     */
    public function terminate(): void;
}
