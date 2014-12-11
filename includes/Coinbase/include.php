<?php

if(!function_exists('curl_init')) {
    throw new Exception('The Coinbase client library requires the CURL PHP extension.');
}

require_once(dirname(__FILE__) . '/CBException.php');
require_once(dirname(__FILE__) . '/ApiException.php');
require_once(dirname(__FILE__) . '/ConnectionException.php');
require_once(dirname(__FILE__) . '/Coinbase.php');
require_once(dirname(__FILE__) . '/Requestor.php');
require_once(dirname(__FILE__) . '/Rpc.php');
require_once(dirname(__FILE__) . '/OAuth.php');
require_once(dirname(__FILE__) . '/TokensExpiredException.php');
require_once(dirname(__FILE__) . '/Authentication.php');
require_once(dirname(__FILE__) . '/SimpleApiKeyAuthentication.php');
require_once(dirname(__FILE__) . '/OAuthAuthentication.php');
require_once(dirname(__FILE__) . '/ApiKeyAuthentication.php');
