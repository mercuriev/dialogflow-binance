<?php
declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\Log\Writer\Stream;
use Laminas\Log\Formatter\Simple;

$aggregator = new ConfigAggregator([
    \Mezzio\Helper\ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Mezzio\Router\AuraRouter\ConfigProvider::class,
    \Laminas\Diactoros\ConfigProvider::class,
    \Laminas\Db\ConfigProvider::class,
    \Laminas\Log\ConfigProvider::class,

    new ArrayProvider([
        // authorized telegram chat id
        'auth_channels' => [
            'DIALOGFLOW_CONSOLE',
            'tg.722234409', // Noonan
            'tg.322021317', // merc
        ],
        'key' => $_ENV['KEY'],
        'binance' => [
            'key'       => $_ENV['API_KEY'],
            'secret'    => $_ENV['API_SECRET']
        ],
        'db' => [
            'driver'    => 'Pdo',
            # cloud dsn: 'mysql:dbname=%s;unix_socket=%s/%s',
            'dsn'       => @$_ENV['DB_DSN']         ?? 'mysql:dbname=noonan;host=10.64.0.1;charset=utf8',
            'username'  => @$_ENV['DB_USERNAME']    ?? 'root',
            'password'  => @$_ENV['DB_PASSWORD']    ?? 'toor',
            'driver_options' => [
                \PDO::ATTR_TIMEOUT => 3,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]
        ],
        'dependencies' => [
            'abstract_factories' => [
                ReflectionBasedAbstractFactory::class
            ],
            'factories' => [
            ]
        ],
        'log' => [
            'writers' => [
                'stderr' => [
                    'name' => Stream::class,
                    'options' => [
                        'stream' => 'php://stderr',
                        'formatter' => [
                            'name' => Simple::class,
                        ]
                    ]
                ]
            ]
        ]
    ])
]);

return $aggregator->getMergedConfig();
