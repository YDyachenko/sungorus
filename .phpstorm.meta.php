<?php

/**
 * PhpStorm code completion
 * Add code completion for PSR-11 Container Interface
 */

namespace PHPSTORM_META {

    use Interop\Container\ContainerInterface as InteropContainerInterface;
    use Psr\Container\ContainerInterface as PsrContainerInterface;

    override(InteropContainerInterface::get(0),
        map([
            '' => '@',
        ])
    );

    override(PsrContainerInterface::get(0),
        map([
            '' => '@',
        ])
    );
}