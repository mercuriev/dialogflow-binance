<?php
namespace Handler;

use Laminas\Diactoros\ServerRequest;

class SyncHandlerTest extends \AbstractTest
{
    public function test()
    {
        $req = (new ServerRequest());
        $sut = self::$sm->get(SyncHandler::class);

        $res = $sut->handle($req);
        $this->assertEquals(200, $res->getStatusCode());
    }
}
