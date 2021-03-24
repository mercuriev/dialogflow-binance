<?php
namespace Action;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Hook\Request;
use Hook\Response;

abstract class AbstractAction implements RequestHandlerInterface
{
    abstract function action(Request $query) : Response;

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $payload = $request->getParsedBody();
        try {
            $query = new Request($payload);
        }
        catch(\LogicException $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }

        $inner = $this->action($query);
        $res = new JsonResponse($inner);
        $res->response = $inner;

        return $res;
    }
}

