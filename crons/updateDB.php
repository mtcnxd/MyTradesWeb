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

$mysql   = new MySQL();
$query   = "INSERT INTO wallet_performance(amount, difference) VALUES ('$currentBalance', '$walletChange')";
$mysql->mySQLquery($query);


/*
	Almacena los datos para el analisis de compras
*/

$currencies_toCheck = [ 'bch_mxn','btc_mxn','eth_mxn','ltc_mxn' ];
$markets = $bitsoWallet->getTicker();

foreach ($markets as $book => $price) {
	if ( in_array($book, $currencies_toCheck) ){
		$query   = "INSERT INTO wallet_analytics(book, price) VALUES ('$book', '$price')";
		$mysql->mySQLquery($query);
	}
}