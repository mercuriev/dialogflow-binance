<?php
use PHPUnit\Framework\TestCase;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;

abstract class AbstractTest extends TestCase
{
    /**
     * @var ServiceManager $sm
     */
    protected static $sm;

    static public function setUpBeforeClass() : void
    {
        self::$sm = require 'config/container.php';

        // We can override in test environment to mock specific services in specific tests
        self::$sm->setAllowOverride(true);

        // Execute programmatic/declarative middleware pipeline and routing
        // configuration statements
        $app = self::$sm->get(Application::class);
        $factory = self::$sm->get(MiddlewareFactory::class);
        (require 'config/pipeline.php')($app, $factory, self::$sm);
        (require 'config/routes.php')($app, $factory, self::$sm);
    }
}
