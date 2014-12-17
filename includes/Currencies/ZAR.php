<?php

namespace Currencies;
use Amount;

class ZAR implements FiatMeta {
	use Common;

	public function getSymbol() {
		return 'R';
	}

	public function getDenominations() {
		return [
			1 => new Amount('10'),
			2 => new Amount('20'),
			3 => new Amount('50'),
			4 => new Amount('100'),
			5 => new Amount('200'),
		];
	}
}