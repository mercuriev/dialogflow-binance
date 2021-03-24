<?php

/**
 * @see       https://github.com/mezzio/mezzio-aurarouter for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-aurarouter/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-aurarouter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Router;

use Aura\Router\Route as AuraRoute;
use Aura\Router\RouterContainer as Router;
use Aura\Router\Rule\Path as PathRule;
use Psr\Http\Message\ServerRequestInterface as Request;

use function array_key_exists;
use function array_merge;
use function array_reduce;
use function array_unique;
use function explode;
use function in_array;

/**
 * Router implementation bridging the Aura.Router.
 *
 * Adds routes to the Aura.Router, using the path as the name, and a
 * middleware value equivalent to the middleware in the Route instance.
 *
 * If HTTP methods are defined (and not the wildcard), they are imploded
 * with a pipe symbol and added as server REQUEST_METHOD criteria.
 *
 * If tokens or values are present in the options array, they are also
 * added to the router.
 */
class AuraRouter implements RouterInterface
{
    /**
     * Map paths to allowed HTTP methods.
     *
     * @var array
     */
    private $pathMethodMap = [];

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @var Route[] Routes aggregated to inject.
     */
    private $routesToInject = [];

    /**
     * Constructor
     *
     * If no Aura.Router instance is provided, the constructor will lazy-load
     * an instance. If you need to customize the Aura.Router instance in any
     * way, you MUST inject it yourself.
     */
    public function __construct(?Router $router = null)
    {
        if (null === $router) {
            $router = $this->createRouter();
        }

        $this->router = $router;
    }

    public function addRoute(Route $route) : void
    {
        $this->routesToInject[] = $route;
    }

    public function match(Request $request) : RouteResult
    {
        // Must inject routes prior to matching.
        $this->injectRoutes();

        $matcher = $this->router->getMatcher();
        $route = $matcher->match($request);

        if (false === $route) {
            return $this->marshalFailedRoute($request, $matcher->getFailedRoute());
        }

        return $this->marshalMatchedRoute($route);
    }

    public function generateUri(string $name, array $substitutions = [], array $options = []) : string
    {
        // Must inject routes prior to generating URIs.
        $this->injectRoutes();

        return $this->router->getGenerator()->generateRaw($name, $substitutions);
    }

    /**
     * Create a default Aura router instance
     */
    private function createRouter() : Router
    {
        return new Router();
    }

    /**
     * Marshal a RouteResult representing a route failure.
     *
     * If the route failure is due to the HTTP method, passes the allowed
     * methods when creating the result.
     */
    private function marshalFailedRoute(
        Request $request,
        AuraRoute $failedRoute = null
    ) : RouteResult {
        // Evidently, getFailedRoute() can sometimes return null; these are 404
        // conditions. Additionally, if the failure is due to inability to
        // match the path, that to is a 404 condition.
        if (null === $failedRoute
            || $failedRoute->failedRule === PathRule::class
        ) {
            return RouteResult::fromRouteFailure(Route::HTTP_METHOD_ANY);
        }

        // Check to see if we have an entry in the method path map; if so,
        // register a 405 using that value.
        [$path] = explode('^', (string) $failedRoute->name);
        if (array_key_exists($path, $this->pathMethodMap)) {
            return RouteResult::fromRouteFailure($this->pathMethodMap[$path]);
        }

        // If the above failed, check to see if the failure was due to method used.
        // This should only occur when HTTP_METHOD_ANY is used; however, in
        // that case, no method should result in failure.
        if ($failedRoute->allows
            && ! in_array($request->getMethod(), $failedRoute->allows)
        ) {
            return RouteResult::fromRouteFailure($failedRoute->allows);
        }

        return RouteResult::fromRouteFailure($failedRoute->allows ?: Route::HTTP_METHOD_ANY);
    }

    /**
     * Marshals a route result based on the matched AuraRoute and request method.
     */
    private function marshalMatchedRoute(AuraRoute $auraRoute) : RouteResult
    {
        $route = $this->matchAuraRouteToRoute($auraRoute);
        if (! $route) {
            // This should likely never occur, but is present for completeness.
            return RouteResult::fromRouteFailure(Route::HTTP_METHOD_ANY);
        }

        return RouteResult::fromRoute($route, $auraRoute->attributes);
    }

    /**
     * Loops through any un-injected routes and injects them into the Aura.Router instance.
     */
    private function injectRoutes() : void
    {
        foreach ($this->routesToInject as $index => $route) {
            $this->injectRoute($route);
            $this->routes[] = $route;
            unset($this->routesToInject[$index]);
        }
    }

    /**
     * Inject a route into the underlying Aura.Router instance.
     */
    private function injectRoute(Route $route) : void
    {
        $path = $route->getPath();

        // Convert Route to AuraRoute
        $auraRoute = new AuraRoute();
        $auraRoute->name($route->getName());
        $auraRoute->path($path);
        $auraRoute->handler($route->getMiddleware());

        foreach ($route->getOptions() as $key => $value) {
            switch ($key) {
                case 'tokens':
                    $auraRoute->tokens($value);
                    break;
                case 'values':
                    $auraRoute->defaults($value);
                    break;
                case 'wildcard':
                    $auraRoute->wildcard($value);
                    break;
            }
        }

        $allowedMethods = $route->getAllowedMethods();
        if (Route::HTTP_METHOD_ANY === $allowedMethods) {
            // Matches any method; no special handling required
            $this->router->getMap()->addRoute($auraRoute);
            return;
        }

        // Inject allowed methods, and map them for 405 detection
        $auraRoute->allows($allowedMethods);
        $this->router->getMap()->addRoute($auraRoute);

        $this->pathMethodMap[$path] = array_key_exists($path, $this->pathMethodMap)
            ? array_unique(array_merge($this->pathMethodMap[$path], $allowedMethods))
            : $allowedMethods;
    }

    /**
     * Match an Aura\Route to a Mezzio\Router\Route.
     *
     * @param AuraRoute $auraRoute
     * @return false|Route False if unable to match to a composed route instance.
     */
    private function matchAuraRouteToRoute(AuraRoute $auraRoute)
    {
        return array_reduce($this->routes, function ($matched, $route) use ($auraRoute) {
            if ($matched) {
                return $matched;
            }

            // We store the route name already, so we can match on that
            if ($auraRoute->name === $route->getName()) {
                return $route;
            }

            return false;
        }, false);
    }
}
