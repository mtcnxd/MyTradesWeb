<?php

require_once ("test.fortech.mx/classes/BitsoWallet.php");
require_once ("test.fortech.mx/classes/mySQL.php");

use classes\BitsoWallet;
use classes\MySQL;

/*
Almacena los datos del balance de la cartera
*/

$bitsoWallet = new BitsoWallet();

$balances_array = $bitsoWallet->getAccountBalance();
$latestBalance  = $bitsoWallet->getLatestBalance();
$currentBalance = array_sum(array_values($balances_array));

$walletChange = (($currentBalance - $latestBalance) / $currentBalance) * 100;
$walletChange = number_format($walletChange, 2);

if ($walletChange <= -1.0){
	$bitsoWallet->sendWebHook('BitsoWallet');
}

$mysql = new MySQL();
$query = "INSERT INTO wallet_performance(amount, difference) VALUES ('$currentBalance', '$walletChange')";
$mysql->mySQLquery($query);

/*
Almacena los datos para el analisis de compras
*/

$favorits = ['btc_mxn','bch_mxn','ltc_mxn','mana_mxn','bat_mxn','eth_mxn'];
$markets  = $bitsoWallet->getFullTicker();

foreach ($markets as $book => $price) {
	if ( in_array($book, $favorits) ){
		$query = "INSERT INTO wallet_analytics(book, price, volume) VALUES ('".$book."', ".$price['last'].",".$price['volum'].")";
		$mysql->mySQLquery($query);
	}
}