<?php

namespace classes;

require_once('mySQL.php');
require_once('Bitso.php');

use classes\MySQL;

class BitsoWallet extends Bitso 
{

	/*
	GENERAL APP AND WEB
	*/

	public function getLatestBalance()
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_performance ORDER BY date DESC LIMIT 1";
		$data  = $mysql->mySQLquery($query);

		$last_balance = 0;
		if(!empty($data)){
			$last_balance = $data[0]->amount;
		}
		return $last_balance;
	}

	public function getCurrentChange($current_balance)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_performance ORDER BY id DESC LIMIT 1";
		$data  = $mysql->mySQLquery($query);

		if(!empty($data)){
			$last_price = $data[0]->amount;
		
			$change = (($current_balance - $last_price) / $current_balance) * 100;
			$change = number_format($change, 2);

			return $change;
		}
	}

	public function getAverageTrades()
	{
		$mysql = new MySQL();
		$query = "SELECT AVG(trades) average FROM (
			SELECT COUNT(*) trades, date_format(date,'%u-%Y') week FROM wallet_balance GROUP BY week) tbl";
		$result = $mysql->mySQLquery($query);

		return $result[0]->average;
	}

	public function getPerformanceIntime($time_elapsed = 12)
	{	
		$mysql = new MySQL();
		$query = "SELECT SUM(difference) as performance FROM (
					SELECT difference FROM wallet_performance ORDER BY date DESC LIMIT $time_elapsed) as AVG";
		$result = $mysql->mySQLquery($query);

		return number_format($result[0]->performance, 2);
	}		

	public function getLatestCurrencySell($book)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_balance WHERE status = 0 AND book = '$book' ORDER BY sell_date DESC LIMIT 1";
		$data  = $mysql->mySQLquery($query);

		if(!empty($data)){
			return $data[0]->price;
		}

	}

	public function getOldestBuy(){
		$mysql = new MySQL();
		$query = "SELECT *, TIMESTAMPDIFF(HOUR, date, now()) as elapsed FROM wallet_balance a 
				  JOIN wallet_currencys b ON a.book = b.book ORDER by date ASC LIMIT 1";
		$data  = $mysql->mySQLquery($query);

		$text  = $this->convertTimeToText($data[0]->elapsed);

		return $text;
	}

	public function getMinimunMaximun($book)
	{
		$mysql = new MySQL();
		$query = "SELECT min(price) minimum, max(price) maximun, ((min(price) - max(price))/max(price))*100 diff, date, volume
				  FROM (SELECT * FROM wallet_analytics WHERE book = '$book' ORDER BY date DESC LIMIT 24) Tbl";
		$data  = $mysql->mySQLquery($query);

		if(!empty($data)){
			return $data[0];
		}				  

	}

	public function getWalletBalances()
	{
		$balances = $this->getBalance();
		$ticker   = $this->getTicker();

		$balanceValue = array();
		foreach ($balances as $key => $value) {

			$book = $value->currency.'_mxn';

			if ($value->currency == 'mxn'){
				$balanceValue[] = [
					'currency' => $value->currency,
					'amount'   => $value->total,
					'value'    => $value->total,
				];
			} else if ($value->total > 0.002){
				if( array_key_exists($book, $ticker) ){
					$balanceValue[] = [
						'currency' => $value->currency,
						'amount'   => $value->total,
						'value'    => $value->total * $ticker[$book],
					];
				} else {
					$book = $value->currency.'_usd';
					if (array_key_exists($book, $ticker)){
						$balanceValue[] = [
							'currency' => $value->currency,
							'amount'   => $value->total,
							'value'    => $value->total * $ticker[$book] * $ticker['usd_mxn'],
						];
					}
				}
			}
		}

		return $balanceValue;

	}

	/*
	ONLY API LEVEL
	*/

	public function getChartMarketData($book = 'btc_mxn')
	{
		$mysql = new MySQL();
		$query = "select id, price, volume, date_format(date, '%H:%i') as date from (
					SELECT * FROM wallet_analytics WHERE book = '$book' ORDER by date desc limit 24
				) tbl order by id asc";
		$result = $mysql->mySQLquery($query);
		return $result;
	}

	public function getChartData($limit = 24)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM (
					SELECT date, amount, difference 
					FROM wallet_performance ORDER BY date DESC LIMIT $limit) tbl ORDER BY date";
		$result = $mysql->mySQLquery($query);
		return $result;
	}

	public function getWalletMainList()
	{
		$mysql = new MySQL();
		$query = "SELECT a.id, a.book, a.price, date_format(a.date, '%d/%m/%Y') as date, b.currency 
				  FROM wallet_balance a JOIN wallet_currencys b ON a.book = b.book WHERE a.status = 1 
				  ORDER BY a.date DESC";
		return $mysql->mySQLquery($query);
	}

	public function getBalanceHistory($limit = 12)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_performance ORDER BY date DESC LIMIT $limit";
		return $mysql->mySQLquery($query);
	}

	public function sendWebHook($event, $data) 
	{
		$url = 'https://maker.ifttt.com/trigger/'.$event.'/with/key/b02sH9pYZV0xykH4H8K2wT';		
		$payload = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_POST, true);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;

	}

	/* 
	FOR TRADE
	*/

	public function updateBuyingPower($data)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_available where currency = 'mxn'";

		if (!is_null( $mysql->mySQLquery($query) )) {
			$mysql->mySQLupdate("wallet_available", $data, "currency = 'mxn'");
		}

	}

	/* 
	FOR DEBUG
	*/

	protected function convertTimeToText($time)
	{
		$days   = ($time/24);
		$hours  = ($time%24);
		$string = number_format($days,0) ." days ". $hours ." hours ago"; 

		return $string;
	}

	public function openLogfile()
	{
		$fcontent = "";
		$filename = 'error_log';

		if (file_exists($filename)){
			$file = fopen($filename, 'r');
			$fcontent = fread($file, filesize($filename));;
		}
		
		return $fcontent;
	}

	public function writeLogfile($filename, $content)
	{
		$date = date('dmY');
		$file = fopen('/home/fortechm/test.fortech.mx/crons/'.$filename.'-'.$date.'.log' , 'a+');
		fwrite($file, 'Content: '. $content);
		fclose($file);
	}	
	
}