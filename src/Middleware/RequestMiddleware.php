<?php
namespace Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Hook\Request;
use Laminas\Diactoros\Response\TextResponse;

final class RequestMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $payload = $request->getParsedBody();
        if ($payload) {
            $request = $request->withAttribute(Request::class, $req = new Request($payload));
            $request = $request->withAttribute('action', $req->getAction());
            return $handler->handle($request);
        } else {
            return new TextResponse('Empty request. Did you set Content-Type?', 400);
        }
    }
}

