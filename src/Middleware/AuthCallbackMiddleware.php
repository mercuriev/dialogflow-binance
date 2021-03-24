<?php
namespace Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Laminas\Diactoros\Response\EmptyResponse;

final class AuthCallbackMiddleware implements MiddlewareInterface
{
    private string $key;

    public function __construct(array $config)
    {
        $this->key = $config['key'];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $key = $request->getHeaderLine('X-Auth');
        if ($key == $this->key) {
            return $handler->handle($request);
        } else {
            return new EmptyResponse(403);
        }
    }
}
