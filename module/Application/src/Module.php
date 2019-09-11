<?php

namespace Application;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Crypt\BlockCipher;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Stdlib\ArrayUtils;

class Module implements ConfigProviderInterface, ConsoleUsageProviderInterface, ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();
        $config   = $provider();

        $configs = [
            'module.config.php',
            'routes.php',
            'controllers.php',
            'tablegateways.php',
        ];

        foreach ($configs as $file) {
            $path   = __DIR__ . '/../config/' . $file;
            $config = ArrayUtils::merge($config, include $path);
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                BlockCipher::class => function () {
                    return BlockCipher::factory('openssl');
                },
            ]];
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleUsage(Console $console)
    {
        return [
            'cron clearKeysTable'       => '[CRONJOB] Remove out-of-date encryption keys from DB',
            'cron clearLogFailureTable' => '[CRONJOB] Remove out-of-date entries from authentication failure log',
        ];
    }
}
