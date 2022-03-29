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

	public function getLatestBuy()
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM `wallet_balance` WHERE status = 0 ORDER BY book";
		$data  = $mysql->mySQLquery();

		if (!empty($data)){
			return $array;
		}
	}

	/*
	ONLY API LEVEL
	*/

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

		$result = $mysql->mySQLquery($query);
		
		return $result;
	}

	public function sendWebHook($event) 
	{
		$url = "https://maker.ifttt.com/trigger/".$event."/with/key/b02sH9pYZV0xykH4H8K2wT";
		
		$values = ["value1" => "Hola", "value2" => "Nuevo", "value3" => "Mundo"];
		$payload = json_encode($values);

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
	FOR DEBUG
	*/

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
		fwrite($file, $content);
		fclose($file);
	}	
	
}