<?php

require_once ('../classes/BitsoWallet.php'); 

use classes\BitsoWallet;
use classes\MySQL;

$request = $_REQUEST['request'];
$data_array = array();

$mysql = new MySQL();
$bitsoWallet = new BitsoWallet();
$bitsoWallet->writeLogfile('android', $request);

switch($request){
	case 'avg':
	
		$mysql_str = "SELECT AVG(trades) average FROM (
		SELECT COUNT(*) trades, date_format(date,'%u-%Y') week FROM wallet_balance GROUP BY week) tbl";
		
		break;
		
	case 'last':
	
		$mysql_str = "SELECT b.currency, date_format(a.date,'%d/%m/%Y') as date, TIMESTAMPDIFF(HOUR, a.date, now()) as elapsed, (SELECT AVG(trades) average FROM (
		SELECT COUNT(*) trades, date_format(date,'%u-%Y') week FROM wallet_balance GROUP BY week) tbl) AS average 
		FROM wallet_balance a LEFT JOIN wallet_currencys b ON a.book = b.book WHERE a.status = 1 ORDER BY a.id DESC LIMIT 1";
		
		break;

	case 'mainList':

		$bitsoTicker = $bitsoWallet->getTicker();
		$response = $bitsoWallet->getWalletMainList();

		$bitsoWallet->writeLogfile('android', $response);

		foreach ($response as $key => $value) {
			$data_array[] = [
				'id' 	   => $value->id,
				'book' 	   => $value->book,
				'current'  => $bitsoTicker[$value->book],				
				'last'     => $value->price,
				'date' 	   => $value->date,
				'currency' => $value->currency,	
			];
		}

		echo json_encode($data_array);

		break;

	case 'statistics':

		$balances_array = $bitsoWallet->getAccountBalance();
		$currentBalance = array_sum(array_values($balances_array));

		$data_array[0] = array(
			"key1" 	 => "CURRENT PERFORMANCE",
			"value1" => '$'. $bitsoWallet->getCurrentChange($currentBalance) .'%',
			"key2" 	 => "PERFORMANCE LAST 24 HOURS",
			"value2" => $bitsoWallet->getPerformanceIntime(24).'%',			
		);

		
		$data_array[1] = array(
			"key1" 	 => "AVG TRADES",
			"value1" => $bitsoWallet->getAverageTrades(),			
			"key2" 	 => "TOTAL WALLET BALANCE",
			"value2" => '$'. number_format( $currentBalance, 2 ),
		);		
		
		echo json_encode($data_array);

		break;

	case 'chartdata':
		$result = $bitsoWallet->getChartData(20);

		foreach ($result as $key => $value) {
			$data_array[] = $value;
		}

		echo json_encode($data_array);		

		break;

	case 'balances':

	break;

}


