<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Core
 */

namespace Zelasli\Core;

/**
 * Zelasli core framework component
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Core
 */
class FrameworkKernel
{
    /**
     * Global shared (singleton) container instance
     * 
     * @var self
     */
    protected static ?FrameworkKernel $instance = null;

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
}
