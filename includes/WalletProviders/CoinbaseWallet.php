<?php

namespace WalletProviders;

use BitcoinTransactions\BlockchainTransaction;
use BitcoinAddress;
use JSON;
use Exception;
use Exceptions\InsufficientFundsException;
use Amount;
use SimpleHTTP;
use Coinbase\Coinbase;

class CoinbaseWallet implements \WalletProvider {

        private $accountId;
        private $apiKey;
        private $apiSecret;
	private $coinbase;

        private function baseURL() {
                return self::$URL . urlencode($this->id) . '/';
        }

        public function configure(array $options) {
                $this->accountId = $options['coinbaseAccountId'];
                $this->apiKey = $options['coinbaseApiKey'];
                $this->apiSecret = $options['coinbaseApiSecret'];
                try {
			$this->coinbase = Coinbase::withApiKey($this->apiKey, $this->apiSecret);
                } catch(\Exception $e) {
                        throw new \ConfigurationException($e->getMessage());
                }

        }
	
	public function verifyOwnership() {
		# Verify check balance and get transactions works
		try {
			$balance = $this->getBalance();
			$txns = $this->coinbase->getTransactions();
                } catch(\Exception $e) {
			if ( strpos($e->getMessage(),'Status code 401')) {
                        	throw new \Exception("Coinbase authentication failed. (Bad Credentials or Account ID?)");
			} else {
                        	throw new \Exception("Coinbase validation failed: " . $e->getMessage());
			}
                }

		return true;
	}
	public function getBalance($confirmations = 1) {
		$balance = 0;
		try {
			$balance = $this->coinbase->getBalanceByAccountId($this->accountId);
			$balance *= 100000000;
		} catch(\Exception $e) {
			throw new \Exception('Error getting balance: ' . $e->getMessage());
		}
		return Amount::fromSatoshis($balance);
	}
	
	public function isConfigured() {
                return isset(
                        $this->accountId,
                        $this->apiKey,
                        $this->apiSecret,
			$this->coinbase
                );
	}
	
	public function getWalletAddress() {
		# TODO What is this really needed for?
		# Return our account ID instead for now
		return $this->accountId;
	}
	
	public function sendTransaction(BitcoinAddress $to, Amount $howMuch) {
		if ($this->getBalance()->isLessThan($howMuch)) {
			throw new InsufficientFundsException();
		}
		$address = $to->get();
		$amt = $howMuch->get();

		# If the amount is < 0.01 coinbase suggests we add a userfee
		$userfee = null;
		if ( $amt < 0.01 ) {
		    $userfee = '0.0001';
		}

		$result = $this->coinbase->sendMoneyFromAccountId(
			$this->accountId,
			$address,
			$howMuch->get(),
			'ATM Transaction',
			$userfee,
			null);

		if ( !$result->success ) {
			throw new \Exception("Encountered Unspecified Transaction Failure with Coinbase");
		}
    
		$txn = array(
		    'tx_hash' => $this->tryGetBlockchainTxnHash( $result->transaction->id ),
		    'message' => 'Sent ' . $howMuch->get() . ' BTC to ' . $address,
		    'notice' => 'Coinbase Transaction: ' . $result->transaction->id
		);
		return new BlockchainTransaction($txn);
	}

	// Coinbase does not set the txn hash on the initial send response
	// so we poll for it a couple times just in case the transaction
	// is sent immediately (which seems to usually happen).
	private function tryGetBlockchainTxnHash( $coinbaseTxnId ) {
		# Try 3 times and then give up
		for ( $i = 0; $i < 3; $i++ ) {
		    // Sleep at least 2 seconds between tries
		    sleep(2);
		    try {
			$transaction = $this->coinbase->getTransaction( $coinbaseTxnId );
			if ( $transaction->hsh != null ) {
				return $transaction->hsh;
			}
		    } catch ( Exception $e ) {
			# Swallow exceptions here because the txn should eventually go
			# through on coinbase' side even if we don't have a hash for it yet.
		    }
		}
		return '';
	}
}

?>
