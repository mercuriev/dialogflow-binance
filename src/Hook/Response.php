<?php
namespace Hook;

class Response extends \ArrayObject
{
    public string $session;
    public string $channel;
    private string $project = 'cryptoasia';

    public function setSession($id)
    {
        $this->session = $id;
        return $this;
    }

    public function addText(string $msg)
    {
        $i = @count($this['fulfillmentMessages']);

        $this['fulfillmentMessages'][$i]['text']['text'][0] = $msg;

        return $this;
    }

    public function prependMessage(string $text) : self
    {
        if (!@is_array($this['fulfillmentMessages'])) {
            $this->addText($text);
        }
        else {
            array_unshift($this['fulfillmentMessages'], [
                'text' => ['text' => [$text]]
            ]);
        }
        return $this;
    }

    public function getMessages() : array
    {
        $msgs = [];

        foreach (@$this['fulfillmentMessages'] ?? [] as $msg) {
            $msgs[] = current($msg['text']['text']);
        }

        return $msgs;
    }

    public function addContext($name, array $params = null, int $lifespan = 100)
    {
        if (!$params) $params = new \stdClass();
        $this['outputContexts'][] = [
            'name' => "projects/{$this->project}/agent/sessions/{$this->session}/contexts/$name",
            'lifespanCount' => $lifespan,
            'parameters' => $params
        ];
        return $this;
    }

    public function addFollowup(string $event, array $params = null)
    {
        $event = [
            'name' => $event,
            'languageCode' => 'en-US'
        ];
        $event['parameters'] = $params ?? new \stdClass();
        $this['followupEventInput'] = $event;
        return $this;
    }

    public function addContextParam(string $context, string $name, $value)
    {

    }
}
