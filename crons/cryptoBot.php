<?php

require_once ("test.fortech.mx/classes/BitsoWallet.php");
require_once ("test.fortech.mx/classes/mySQL.php");

use classes\BitsoWallet;
use classes\MySQL;

$bitsoWallet = new BitsoWallet();
$ticker = $bitsoWallet->getTicker();

$books  = ['ltc_mxn','bat_mxn'];

/*
foreach ($books as $book) {
	$prices = $bitsoWallet->getLastBoughtPrices($book);	
	$change = (($ticker[$book] - $prices->price)/$ticker[$book]) *100 ;

	if ($change < -4.5){
		$mysql = new MySQL();
		$query = "Insert Into wallet_test(price) VALUES ('".$ticker[$book]."')";
		$mysql->mySQLquery($query);
	}
}
*/

$prices = $bitsoWallet->getLastBoughtPrices('ltc_mxn');
$percent = (($ticker['ltc_mxn'] - $prices->price)/$ticker['ltc_mxn']) *100 ;

if ($percent < -4.5){
	$mysql = new MySQL();
	$query = "Insert Into wallet_test(price) VALUES ('".$ticker['ltc_mxn']."')";
	$mysql->mySQLquery($query);
}



$prices = $bitsoWallet->getLastBoughtPrices('bat_mxn');
$percent = (($ticker['bat_mxn'] - $prices->price)/$ticker['bat_mxn']) *100 ;

if ($percent < -4.5){
	$mysql = new MySQL();
	$query = "Insert Into wallet_test(price) VALUES ('".$ticker['bat_mxn']."')";
	$mysql->mySQLquery($query);
}