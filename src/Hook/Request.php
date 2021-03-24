<?php
namespace Hook;

use Laminas\Diactoros\ServerRequest;

class Request extends ServerRequest implements \ArrayAccess
{
    public static function factory()
    {
        return static::fromGlobals();
    }

    public static function fromGlobals() : self
    {
        $origFactory = function(
            array $server = null,
            array $query = null,
            array $body = null,
            array $cookies = null,
            array $files = null
            ) {
            $server = normalizeServer(
                $server ?: $_SERVER,
                is_callable(self::$apacheRequestHeaders) ? self::$apacheRequestHeaders : null
            );
            $files   = normalizeUploadedFiles($files ?: $_FILES);
            $headers = marshalHeadersFromSapi($server);

            if (null === $cookies && array_key_exists('cookie', $headers)) {
                $cookies = parseCookieHeader($headers['cookie']);
            }

            return new static(
                $server,
                $files,
                marshalUriFromSapi($server, $headers),
                marshalMethodFromSapi($server),
                'php://input',
                $headers,
                $cookies ?: $_COOKIE,
                $query ?: $_GET,
                $body ?: $_POST,
                marshalProtocolVersionFromSapi($server)
            );
        };
        return $origFactory();
    }

    public static function fromJson($body) : self
    {
        $ar = json_decode($body, true);
        return (new static($ar))->withParsedBody($ar);
    }

    public function toResponse($copymsgs = false) : Response
    {
        $res = new Response;
        $res->setSession($this->getSession());
        $res->channel = $this->getChannel();

        if ($copymsgs && @$msgs = $this['queryResult']['fulfillmentMessages']) {
            $res['fulfillmentMessages'] = $msgs;
        }

        return $res;
    }

    public function getQueryText() : string
    {
        return @strval($this['queryResult']['queryText']);
    }

    public function getChannel() : string
    {
        $req =& $this['originalDetectIntentRequest'];
        switch (@$req['source']) {
            case 'telegram':
                return "tg.".$req['payload']['data']['chat']['id'];

            case 'DIALOGFLOW_CONSOLE':
                // single session for dev
                return 'DIALOGFLOW_CONSOLE.'.$this->getSession();

            case null:
                // Web Messenger
                return 'www.'.$this->getSession();

            default:
                throw new \UnexpectedValueException('Unknown channel: '.@$req['source']);
        }
    }

    public function isComplete() : bool
    {
        return (bool) @$this['queryResult']['allRequiredParamsPresent'];
    }

    public function getSession() : string
    {
        return array_reverse(explode('/', $this['session']))[0];
    }

    public function getAction() : ?string
    {
        return @$this['queryResult']['action'];
    }

    public function getAllParams()
    {
        return @$this['queryResult']['parameters'];
    }

    public function getContexts() : array
    {
        $out = [];
        foreach (@$this['queryResult']['outputContexts'] ?? [] as $context) {
            $name = array_reverse(explode('/', $context['name']))[0];
            $out[$name] = @$context['parameters'] ?? [];
        }
        return $out;
    }

    public function getContextParams(string $name) : ?array
    {
        foreach ($this['queryResult']['outputContexts'] as $context) {
            if ($name == array_reverse(explode('/', $context['name']))[0]) {
                return $context['parameters'];
            }
        }
    }

    public function getParam($name)
    {
        return @$this['queryResult']['parameters'][$name];
    }

    public function getText() : string
    {
        return (string) @$this['queryResult']['fulfillmentText'];
    }

    public function offsetExists ($offset)
    {
        $body = $this->getParsedBody();
        return isset($body[$offset]);
    }
	public function offsetGet ($offset)
	{
	    $body = $this->getParsedBody();
        return $body[$offset];
	}
	public function offsetSet ($offset, $value)
	{
	    throw new \LogicException('Trying to change an immutable object.');
	}
	public function offsetUnset ($offset)
	{
	    throw new \LogicException('Trying to change an immutable object.');
	}
}
