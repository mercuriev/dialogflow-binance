<?php
namespace Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    abstract public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface;
}

