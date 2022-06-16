<?php

namespace classes;

require_once('mySQL.php');

use classes\MySQL;

class Bitso {

	public $bitsoKey 	= "TMJEPCYmIv";
	public $bitsoSecret = "d181cda5b0f939ee1b42e7b45ebd93e5";
	public $HTTPMethod  = "GET";

	protected function getBitsoRequest($url)
	{
		$nonce = (integer)round(microtime(true) * 10000 * 100);
		
		// Create signature
		$JSONPayload = "";
		$message = $nonce . $this->HTTPMethod.$url.$JSONPayload;
		$signature = hash_hmac('sha256', $message, $this->bitsoSecret);
		
		// Build the auth header
		$format = 'Bitso %s:%s:%s';
		$authHeader =  sprintf($format, $this->bitsoKey, $nonce, $signature);	 

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.bitso.com". $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, "true");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: '. $authHeader,'Content-Type: application/json'));
		$response = curl_exec($ch);

		return $response;
	}

	public function getFullTicker()
	{
		$payload = $this->getBitsoRequest("/v3/ticker/");
		
		$json   = json_decode($payload);
		$ticker = $json->payload;
		$currencys = array();		

		foreach ($ticker as $value) {
			if( strpos($value->book, "_mxn") or strpos($value->book, "_usd") ){
				$currencys[$value->book] = [
					"last"   => $value->last,
					"high"   => $value->high,
					"low"    => $value->low,
					"volum"  => $value->volume,
					"change" => $value->change_24
				];
			}
		}

		return $currencys;
	}

	public function getTicker()
	{
		$payload = $this->getBitsoRequest("/v3/ticker/");
		
		$json   = json_decode($payload);
		$ticker = $json->payload;
		$currencys = array();		

		foreach ($ticker as $value) {
			if( strpos($value->book, "_mxn") or strpos($value->book, "_usd") ){
				$currencys[$value->book] = $value->last;
			}
		}

		return $currencys;
	}

	public function getBalance()
	{
		$payload = $this->getBitsoRequest("/v3/balance/");
		$json = json_decode($payload);
		$balance = $json->payload;
		$result = array();

		foreach($balance->balances as $value){
			$result[] = $value;
		}

		return $result;
	}


	public function getAccountBalance()
	{
		$ticker  = $this->getTicker();
		$balance = $this->getBalance();
		$balance_mxn = array();

		foreach ($balance as $key => $value) {
			if ($value->total > 0.0002){
				$mxn_book = $value->currency .'_mxn';
				$usd_book = $value->currency .'_usd';

				if ($value->currency == 'mxn'){
					$balance_mxn[$value->currency] = $value->total;
				} else {
					if (array_key_exists($mxn_book, $ticker)){
						$total = $value->total * $ticker[$mxn_book];
						$balance_mxn[$value->currency] = $total;
					} else {
						$total = $value->total * $ticker[$usd_book] * $ticker['usd_mxn'];
						$balance_mxn[$value->currency] = $total;
					}
				}

			}
		}
		return $balance_mxn;

	}
	

}