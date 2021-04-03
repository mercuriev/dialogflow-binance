<?php
namespace Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Hook\Request;

final class TelegramAuthMiddleware extends AbstractMiddleware
{
    private array $ids = [];

    public function __construct(array $config)
    {
        $this->ids = $config['auth_channels'];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $req = $request->getAttribute(Request::class);

        $id = $req->getChannel();
        if (in_array($id, $this->ids) || 0 === strpos($id, 'DIALOGFLOW_CONSOLE.')) {
            return $handler->handle($request);
        }

        return new JsonResponse(['id' => $id], 401);
    }
}
