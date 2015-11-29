<?php

namespace WalletProviders;
use BitcoinTransactions\BlockchainTransaction;
use BitcoinAddress;
use JSON;
use Exception;
use Exceptions\InsufficientFundsException;
use Amount;
use SimpleHTTP;

class Dummy implements \WalletProvider {
	
	public function configure(array $options) {
		# Nothing to configure
	}
	
	public function verifyOwnership() {
		# Nothing to verify
		
		return true;
	}
	
	public function getBalance($confirmations = 1) {
		# Have a forever replenished balance of 1.0 bitcoins :-)
		return Amount::fromSatoshis(100000000);
	}
	
	public function isConfigured() {
		# Always configured because there is nothing to configure
		return true;
	}
	
	public function getWalletAddress() {
		# Dummy Address
		return "123ABC";
	}
	
	public function sendTransaction(BitcoinAddress $to, Amount $howMuch) {
		if ($this->getBalance()->isLessThan($howMuch)) {
			# If someone wants more than 1 bitcoins they're SOL
			throw new InsufficientFundsException();
		}

		# Otherwise always succeeds
		$txn = array(
		    'tx_hash' => '',
		    'message' => 'Message',
		    'notice' => 'Notice'
		);
		return new BlockchainTransaction($txn);
	}
}

?>
