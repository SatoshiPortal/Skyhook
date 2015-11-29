<?php

namespace Coinbase;

class OAuthAuthentication extends Authentication
{
    private $_oauth;
    private $_tokens;

    public function __construct($oauth, $tokens)
    {
        $this->_oauth = $oauth;
        $this->_tokens = $tokens;
    }

    public function getData()
    {
        $data = new stdClass();
        $data->oauth = $this->_oauth;
        $data->tokens = $this->_tokens;
        return $data;
    }
}
