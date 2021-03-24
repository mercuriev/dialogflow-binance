<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Mezzio\Router\AuraRouter;
use Mezzio\Router\Route;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\Stratigility\EmptyPipelineHandler;
use Mezzio\Router\RouteResult;
use Action\BalanceAction;
use Laminas\Diactoros\Response\JsonResponse;

/**
 * Aura.Router route configuration
 *
 * @see http://auraphp.com/packages/3.x/Router/defining-routes.html#2-4-2
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/{id}', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/{id}', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/{id}', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->any("/", function(RequestInterface $req) use ($factory, $container)
    {
        $actions = [
            'balance'       => BalanceAction::class
        ];

        // actions definitions, routing features may be used
        $router = new AuraRouter();
        foreach ($actions as $pattern => $class) {
            $router->addRoute(new Route($pattern, $factory->lazy($class)));

        }

        // match and run action
        $action = $req->getAttribute('action');
        if ($action) {
            $routeResult = $router->match((new ServerRequest())->withUri((new Uri())->withPath($action)));
            if ($routeResult->isSuccess()) {
                return $routeResult->getMatchedRoute()->getMiddleware()
                    ->process($req, new EmptyPipelineHandler(RouteResult::class));
            }
        }

        return new JsonResponse([
            'action' => $action
        ], 404);
    });
};
