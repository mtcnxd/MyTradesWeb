<?php

require_once ('../classes/BitsoWallet.php'); 

use classes\BitsoWallet;
use classes\MySQL;

$request = 'mainList';
$data_array = array();

$mysql = new MySQL();
$bitsoWallet = new BitsoWallet();

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

}


