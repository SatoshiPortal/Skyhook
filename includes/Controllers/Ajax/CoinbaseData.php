<?php

namespace Controllers\Ajax;
use Controllers\Controller;
//use Container;
//use Environment\Post;
use JSON;
use Coinbase\Coinbase;

class CoinbaseData implements Controller {
	public function execute(array $matches, $url, $rest) {
		#$post = Container::dispense("Environment\\Post");
		$apikey = $this->getWallet('coinbaseApiKey');
		$apisecret = $this->getWallet('coinbaseApiSecret');

		$errors = '';
		$balance = '';

		# Start with an empty array of payment methods
		$paymentMethods = array('payment_methods' => array());
		try {
			$coinbase = Coinbase::withApiKey( $apikey, $apisecret);
			$paymentMethods = $coinbase->getPaymentMethods();
		} catch ( \Exception $e ) {
			if ( strpos($e->getMessage(),'401') > -1) {
				$errors = "Coinbase Authentication Failed.";
			} else {
				$errors = "Error: " . $e->getMessage();
			}
		}
		echo JSON::encode([
			'methods' => $paymentMethods,
			'errors' => $errors,
		]);
		return true;
	}

	private function getWallet($index) {
		if ( array_key_exists( 'wallet', $_POST )) {
			$wallet = $_POST['wallet'];
			if ( array_key_exists( $index, $wallet ))
				return $wallet[$index];
		}
		return '';
	}
}
